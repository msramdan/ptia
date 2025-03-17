<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class HasilEvaluasiController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:hasil evaluasi view', only: ['index']),
        ];
    }

    public function index(): View|JsonResponse
    {
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
                    DB::raw('COALESCE(avg_scores.avg_skor_level_4, 0) AS avg_skor_level_4'),
                    DB::raw("COALESCE(indikator_3.kriteria_dampak, '-') AS kriteria_dampak_level_3"),
                    DB::raw("COALESCE(indikator_4.kriteria_dampak, '-') AS kriteria_dampak_level_4")
                )
                ->leftJoinSub(
                    DB::table('project_skor_responden')
                        ->select(
                            'project_id',
                            DB::raw("COALESCE(ROUND(AVG((COALESCE(skor_level_3_alumni, 0) + COALESCE(skor_level_3_atasan, 0)) / 2), 2), 0) AS avg_skor_level_3"),
                            DB::raw("COALESCE(ROUND(AVG((COALESCE(skor_level_4_alumni, 0) + COALESCE(skor_level_4_atasan, 0)) / 2), 2), 0) AS avg_skor_level_4")
                        )
                        ->groupBy('project_id'),
                    'avg_scores',
                    'project.id',
                    '=',
                    'avg_scores.project_id'
                )
                ->leftJoin('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
                ->leftJoin('users', 'project.user_id', '=', 'users.id')
                ->leftJoin('indikator_dampak AS indikator_3', function ($join) {
                    $join->on('project.diklat_type_id', '=', 'indikator_3.diklat_type_id')
                        ->whereRaw('COALESCE(avg_scores.avg_skor_level_3, 0) BETWEEN indikator_3.nilai_minimal AND indikator_3.nilai_maksimal');
                })
                ->leftJoin('indikator_dampak AS indikator_4', function ($join) {
                    $join->on('project.diklat_type_id', '=', 'indikator_4.diklat_type_id')
                        ->whereRaw('COALESCE(avg_scores.avg_skor_level_4, 0) BETWEEN indikator_4.nilai_minimal AND indikator_4.nilai_maksimal');
                })
                ->where('project.status', 'Pelaksanaan')
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
        return view('hasil-evaluasi.index');
    }

    public function showLevel3($id)
    {
        $project = DB::table('project')
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();

        return view('hasil-evaluasi.detail-skor-level3', compact('project'));
    }

    public function showLevel4($id)
    {
        $project = DB::table('project')
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();
        return view('hasil-evaluasi.detail-skor-level4', compact('project'));
    }
}
