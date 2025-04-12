<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->has('tahun') ? $request->query('tahun') : date('Y');

        if (request()->ajax()) {
            $projects = DB::table('project')
                ->select(
                    'project.id',
                    'project.kode_project',
                    'project.kaldikID',
                    'project.kaldikDesc',
                    'project.diklat_type_id',
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
                                "COALESCE((
                                SELECT bobot_aspek_sekunder
                                FROM project_bobot_aspek_sekunder
                                WHERE project_bobot_aspek_sekunder.project_id = project_skor_responden.project_id
                                AND EXISTS (
                                    SELECT 1
                                    FROM project_data_sekunder
                                    WHERE project_data_sekunder.project_id = project_skor_responden.project_id
                                    AND project_data_sekunder.nilai_kinerja_awal < project_data_sekunder.nilai_kinerja_akhir
                                )
                            ), 0) AS bobot_aspek_sekunder"
                            ),
                            DB::raw(
                                "LEAST(100,
                                (COALESCE(ROUND(AVG((COALESCE(skor_level_4_alumni, 0) + COALESCE(skor_level_4_atasan, 0))), 2), 0) +
                                COALESCE((
                                    SELECT bobot_aspek_sekunder
                                    FROM project_bobot_aspek_sekunder
                                    WHERE project_bobot_aspek_sekunder.project_id = project_skor_responden.project_id
                                    AND EXISTS (
                                        SELECT 1
                                        FROM project_data_sekunder
                                        WHERE project_data_sekunder.project_id = project_skor_responden.project_id
                                        AND project_data_sekunder.nilai_kinerja_awal < project_data_sekunder.nilai_kinerja_akhir
                                    )
                                ), 0))
                            ) AS final_avg_skor_level_4"
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
                        ->whereRaw('
                        COALESCE(avg_scores.avg_skor_level_3, 0) > indikator_3.nilai_minimal
                        AND
                        COALESCE(avg_scores.avg_skor_level_3, 0) <= indikator_3.nilai_maksimal
                    ');
                })
                ->leftJoin('indikator_dampak AS indikator_4', function ($join) {
                    $join->on('project.diklat_type_id', '=', 'indikator_4.diklat_type_id')
                        ->whereRaw('
                        COALESCE(avg_scores.final_avg_skor_level_4, 0) > indikator_4.nilai_minimal
                        AND
                        COALESCE(avg_scores.final_avg_skor_level_4, 0) <= indikator_4.nilai_maksimal
                    ');
                })
                ->where('project.status', 'Pelaksanaan')
                ->whereYear('project.created_at', $tahun)
                ->orderByDesc('project.id');


            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('nama_project', fn($row) => e($row->kaldikDesc))
                ->addColumn('user', function ($row) {
                    $avatar = $row->user_avatar
                        ? asset("storage/uploads/avatars/{$row->user_avatar}")
                        : "https://www.gravatar.com/avatar/" . md5(strtolower(trim($row->user_email))) . "&s=450";
                    return '<div class="d-flex align-items-center">
                                <img src="' . e($avatar) . '" class="img-thumbnail" style="width: 50px; height: 50px; border-radius: 5%; margin-right: 10px;">
                                <span>' . e($row->user_name) . '</span>
                            </div>';
                })
                ->addColumn('avg_skor_level_3', fn($row) => '<a href="' . e(url("/hasil-evaluasi/level-3/{$row->id}")) . '"  class="btn btn-link">' . e($row->avg_skor_level_3) . '</a>')
                ->addColumn('avg_skor_level_4', fn($row) => '<a href="' . e(url("/hasil-evaluasi/level-4/{$row->id}")) . '"  class="btn btn-link">' . e($row->avg_skor_level_4) . '</a>')
                ->addColumn('kriteria_dampak_level_3', fn($row) => e($row->kriteria_dampak_level_3 ?? '-'))
                ->addColumn('kriteria_dampak_level_4', fn($row) => e($row->kriteria_dampak_level_4 ?? '-'))
                ->rawColumns(['user', 'avg_skor_level_3', 'avg_skor_level_4'])
                ->toJson();
        }

        // Jumlah project
        $jumlahProject = DB::table('project')
            ->where('status', 'Pelaksanaan')
            ->whereYear('created_at', $tahun)
            ->count();

        // Jumlah responden
        $jumlahResponden = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.created_at', $tahun)
            ->count();

        // Jumlah sudah isi kuesioner alumni
        $sudahAlumni = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.created_at', $tahun)
            ->where('project_responden.status_pengisian_kuesioner_alumni', 'sudah')
            ->count();

        // Persentase sudah isi kuesioner alumni
        $persentaseSudah = $jumlahResponden > 0 ? round(($sudahAlumni / $jumlahResponden) * 100, 2) : 0;

        // Total atasan (yang nama_atasan tidak null)
        $totalAtasan = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.created_at', $tahun)
            ->whereNotNull('project_responden.nama_atasan')
            ->count();

        // Jumlah sudah isi kuesioner atasan
        $sudahAtasan = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.created_at', $tahun)
            ->whereNotNull('project_responden.nama_atasan')
            ->where('project_responden.status_pengisian_kuesioner_atasan', 'sudah')
            ->count();

        // Persentase sudah isi kuesioner atasan
        $persentaseSudahAtasan = $totalAtasan > 0 ? round(($sudahAtasan / $totalAtasan) * 100, 2) : 0;

        return view('dashboard', compact(
            'tahun',
            'jumlahProject',
            'jumlahResponden',
            'persentaseSudah',
            'persentaseSudahAtasan'
        ));
    }
}
