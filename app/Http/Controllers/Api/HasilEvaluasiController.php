<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilEvaluasiController extends Controller
{
    public function getByKaldikID(Request $request, $kaldikID): JsonResponse
    {
        try {
            // Force JSON response
            $request->headers->set('Accept', 'application/json');

            $project = DB::table('project')
                ->select(
                    'project.id',
                    'project.kode_project',
                    'project.kaldikID',
                    'project.kaldikDesc',
                    'project.diklat_type_id',
                    'project.created_at',
                    'project.tanggal_selesai',
                    'diklat_type.nama_diklat_type',
                    'users.name as user_name',
                    'users.avatar as user_avatar',
                    'users.email as user_email',
                    DB::raw('COALESCE(avg_scores.avg_skor_level_3, 0) AS avg_skor_level_3'),
                    DB::raw('COALESCE(avg_scores.final_avg_skor_level_4, 0) AS avg_skor_level_4'),
                    DB::raw("COALESCE(indikator_3.kriteria_dampak, '-') AS kriteria_dampak_level_3"),
                    DB::raw("COALESCE(indikator_4.kriteria_dampak, '-') AS kriteria_dampak_level_4")
                )
                ->leftJoinSub(
                    DB::table('project_skor_responden')
                        ->select(
                            'project_skor_responden.project_id',
                            DB::raw("LEAST(100, COALESCE(ROUND(AVG((COALESCE(skor_level_3_alumni, 0) + COALESCE(skor_level_3_atasan, 0))), 2), 0)) AS avg_skor_level_3"),
                            DB::raw("LEAST(100, COALESCE(ROUND(AVG((COALESCE(skor_level_4_alumni, 0) + COALESCE(skor_level_4_atasan, 0))), 2), 0)) AS base_avg_skor_level_4"),
                            DB::raw(
                                "COALESCE((SELECT bobot_aspek_sekunder FROM project_bobot_aspek_sekunder WHERE project_bobot_aspek_sekunder.project_id = project_skor_responden.project_id AND EXISTS (SELECT 1 FROM project_data_sekunder WHERE project_data_sekunder.project_id = project_skor_responden.project_id AND project_data_sekunder.nilai_kinerja_awal < project_data_sekunder.nilai_kinerja_akhir)), 0) AS bobot_aspek_sekunder"
                            ),
                            DB::raw(
                                "LEAST(100, (COALESCE(ROUND(AVG((COALESCE(skor_level_4_alumni, 0) + COALESCE(skor_level_4_atasan, 0))), 2), 0) + COALESCE((SELECT bobot_aspek_sekunder FROM project_bobot_aspek_sekunder WHERE project_bobot_aspek_sekunder.project_id = project_skor_responden.project_id AND EXISTS (SELECT 1 FROM project_data_sekunder WHERE project_data_sekunder.project_id = project_skor_responden.project_id AND project_data_sekunder.nilai_kinerja_awal < project_data_sekunder.nilai_kinerja_akhir)), 0))) AS final_avg_skor_level_4"
                            )
                        )
                        ->groupBy('project_skor_responden.project_id'),
                    'avg_scores',
                    'project.id',
                    '=',
                    'avg_scores.project_id'
                )
                ->leftJoin('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
                ->leftJoin('users', 'project.user_id', '=', 'users.id')
                ->leftJoin('indikator_dampak AS indikator_3', function ($join) {
                    $join->on('project.diklat_type_id', '=', 'indikator_3.diklat_type_id')
                        ->whereRaw('COALESCE(avg_scores.avg_skor_level_3, 0) > indikator_3.nilai_minimal AND COALESCE(avg_scores.avg_skor_level_3, 0) <= indikator_3.nilai_maksimal');
                })
                ->leftJoin('indikator_dampak AS indikator_4', function ($join) {
                    $join->on('project.diklat_type_id', '=', 'indikator_4.diklat_type_id')
                        ->whereRaw('COALESCE(avg_scores.final_avg_skor_level_4, 0) > indikator_4.nilai_minimal AND COALESCE(avg_scores.final_avg_skor_level_4, 0) <= indikator_4.nilai_maksimal');
                })
                ->where('project.kaldikID', $kaldikID)
                ->where('project.status', 'Pelaksanaan')
                ->first();

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found',
                    'data' => null
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            // Format the response data
            $responseData = [
                'id' => $project->id,
                'kode_project' => $project->kode_project,
                'kaldikID' => $project->kaldikID,
                'kaldikDesc' => $project->kaldikDesc,
                'diklat_type' => [
                    'id' => $project->diklat_type_id,
                    'nama' => $project->nama_diklat_type,
                ],
                'created_at' => $project->created_at,
                'tanggal_selesai' => $project->tanggal_selesai,
                'evaluator' => [
                    'name' => $project->user_name,
                    'avatar' => $project->user_avatar ? asset("storage/uploads/avatars/{$project->user_avatar}") : "https://www.gravatar.com/avatar/" . md5(strtolower(trim($project->user_email))) . "&s=450",
                    'email' => $project->user_email,
                ],
                'evaluasi' => [
                    'level_3' => [
                        'avg_skor' => (float) $project->avg_skor_level_3,
                        'kriteria_dampak' => $project->kriteria_dampak_level_3,
                    ],
                    'level_4' => [
                        'avg_skor' => (float) $project->avg_skor_level_4,
                        'kriteria_dampak' => $project->kriteria_dampak_level_4,
                    ],
                ],
                'links' => [
                    'detail_level_3' => url("/hasil-evaluasi/level-3/{$project->id}"),
                    'detail_level_4' => url("/hasil-evaluasi/level-4/{$project->id}"),
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $responseData
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data: ' . $e->getMessage(),
                'data' => null
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }
}
