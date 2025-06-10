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
        $tahun = $request->input('tahun', date('Y'));
        $selectedTriwulan = $request->input('triwulan', 'all');

        // Fungsi untuk menentukan tanggal mulai dan selesai berdasarkan triwulan
        $getDatesBasedOnTriwulan = function ($tahunInput, $triwulanInput) {
            $startDate = Carbon::createFromDate($tahunInput, 1, 1)->startOfDay(); // Selalu mulai dari awal tahun
            $endDate = Carbon::createFromDate($tahunInput, 12, 31)->endOfDay(); // Default akhir tahun

            if ($triwulanInput != 'all') {
                switch ($triwulanInput) {
                    case '1':
                        $endDate = Carbon::createFromDate($tahunInput, 3, 1)->endOfMonth()->endOfDay();
                        break;
                    case '2':
                        $endDate = Carbon::createFromDate($tahunInput, 6, 1)->endOfMonth()->endOfDay();
                        break;
                    case '3':
                        $endDate = Carbon::createFromDate($tahunInput, 9, 1)->endOfMonth()->endOfDay();
                        break;
                    case '4':
                        // Untuk triwulan 4, endDate tetap akhir Desember
                        break;
                }
            }
            return ['start' => $startDate, 'end' => $endDate];
        };

        $dates = $getDatesBasedOnTriwulan($tahun, $selectedTriwulan);
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        // Query untuk menghitung persentase dampak
        $projects = DB::table('project')
            ->select(
                'project.id',
                'project.diklat_type_id',
                DB::raw('COALESCE(avg_scores.avg_skor_level_3, 0) AS avg_skor_level_3'),
                DB::raw('COALESCE(avg_scores.final_avg_skor_level_4, 0) AS avg_skor_level_4')
            )
            ->leftJoinSub(
                DB::table('project_skor_responden')
                    ->select(
                        'project_id',
                        DB::raw('LEAST(100, COALESCE(ROUND(AVG((COALESCE(skor_level_3_alumni, 0) + COALESCE(skor_level_3_atasan, 0))), 2), 0)) AS avg_skor_level_3'),
                        DB::raw('LEAST(100, COALESCE(ROUND(AVG((COALESCE(skor_level_4_alumni, 0) + COALESCE(skor_level_4_atasan, 0))), 2), 0) +
                        COALESCE((SELECT bobot_aspek_sekunder FROM project_bobot_aspek_sekunder
                        WHERE project_bobot_aspek_sekunder.project_id = project_skor_responden.project_id
                        AND EXISTS (SELECT 1 FROM project_data_sekunder
                        WHERE project_data_sekunder.project_id = project_skor_responden.project_id
                        AND project_data_sekunder.nilai_kinerja_awal < project_data_sekunder.nilai_kinerja_akhir)), 0)) AS final_avg_skor_level_4')
                    )
                    ->groupBy('project_id'),
                'avg_scores',
                'project.id',
                '=',
                'avg_scores.project_id'
            )
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.tanggal_selesai', $tahun)
            ->whereBetween('project.tanggal_selesai', [$startDate, $endDate])
            ->when($request->evaluator, function ($query, $evaluator) {
                $query->where('project.user_id', $evaluator);
            })
            ->when($request->diklat_type, function ($query, $diklatType) {
                $query->where('project.diklat_type_id', $diklatType);
            })
            ->get();

        // Calculate percentages for level 3 and level 4 separately
        $totalProjects = $projects->count();
        $impactfulLevel3 = $projects->filter(function ($project) {
            return $project->avg_skor_level_3 >= 50; // Cukup Berdampak atau lebih
        })->count();
        $impactfulLevel4 = $projects->filter(function ($project) {
            return $project->avg_skor_level_4 >= 50; // Cukup Berdampak atau lebih
        })->count();

        $percentageLevel3 = $totalProjects > 0 ? ($impactfulLevel3 / $totalProjects) * 100 : 0;
        $percentageLevel4 = $totalProjects > 0 ? ($impactfulLevel4 / $totalProjects) * 100 : 0;

        // Query dasar untuk statistik dan DataTables
        $baseProjectsQuery = DB::table('project')
            ->where('status', 'Pelaksanaan')
            ->whereYear('tanggal_selesai', $tahun)
            ->whereBetween('project.tanggal_selesai', [$startDate, $endDate]);

        if ($request->ajax() && !$request->has('draw')) { // Permintaan AJAX untuk statistik
            $statsQuery = clone $baseProjectsQuery;
            $respondenBaseQuery = DB::table('project_responden')
                ->join('project', 'project_responden.project_id', '=', 'project.id')
                ->where('project.status', 'Pelaksanaan')
                ->whereYear('project.tanggal_selesai', $tahun)
                ->whereBetween('project.tanggal_selesai', [$startDate, $endDate]);

            $jumlahProject = $statsQuery->count();
            $jumlahResponden = (clone $respondenBaseQuery)->count();
            $sudahAlumni = (clone $respondenBaseQuery)
                ->where('project_responden.status_pengisian_kuesioner_alumni', 'sudah')
                ->count();
            $persentaseSudah = $jumlahResponden > 0 ? round(($sudahAlumni / $jumlahResponden) * 100, 2) : 0;

            $totalAtasan = (clone $respondenBaseQuery)
                ->whereNotNull('project_responden.nama_atasan')
                ->count();
            $sudahAtasan = (clone $respondenBaseQuery)
                ->whereNotNull('project_responden.nama_atasan')
                ->where('project_responden.status_pengisian_kuesioner_atasan', 'sudah')
                ->count();
            $persentaseSudahAtasan = $totalAtasan > 0 ? round(($sudahAtasan / $totalAtasan) * 100, 2) : 0;

            // Query untuk summary DataTables (rata-rata skor)
            $summaryProjectsQuery = DB::table('project')
                ->select(DB::raw('COALESCE(avg_scores.avg_skor_level_3, 0) AS avg_skor_level_3'), DB::raw('COALESCE(avg_scores.final_avg_skor_level_4, 0) AS avg_skor_level_4'))
                ->leftJoinSub(
                    DB::table('project_skor_responden')
                        ->select(
                            'project_skor_responden.project_id',
                            DB::raw("LEAST(100, COALESCE(ROUND(AVG((COALESCE(skor_level_3_alumni, 0) + COALESCE(skor_level_3_atasan, 0))), 2), 0)) AS avg_skor_level_3"),
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
                ->where('project.status', 'Pelaksanaan')
                ->whereYear('project.tanggal_selesai', $tahun)
                ->whereBetween('project.tanggal_selesai', [$startDate, $endDate]);

            return response()->json([
                'jumlahProject' => $jumlahProject,
                'jumlahResponden' => $jumlahResponden,
                'sudahAlumni' => $sudahAlumni,
                'persentaseSudah' => $persentaseSudah,
                'sudahAtasan' => $sudahAtasan,
                'persentaseSudahAtasan' => $persentaseSudahAtasan,
                'summary' => [
                    'avg_skor_level_3' => $summaryProjectsQuery->avg('avg_skor_level_3'),
                    'avg_skor_level_4' => $summaryProjectsQuery->avg('final_avg_skor_level_4'),
                ],
                'impact_percentage' => [
                    'level_3' => round($percentageLevel3, 2),
                    'level_4' => round($percentageLevel4, 2)
                ]
            ]);
        }

        if (request()->ajax() && $request->has('draw')) { // Permintaan DataTables
            $projectsQuery = DB::table('project')
                ->select(
                    'project.id',
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
                    DB::raw("COALESCE(indikator_4.kriteria_dampak, '-') AS kriteria_dampak_level_4"),
                    'project.tanggal_selesai'
                )
                ->leftJoinSub(
                    DB::table('project_skor_responden')
                        ->select(
                            'project_skor_responden.project_id',
                            DB::raw("LEAST(100, COALESCE(ROUND(AVG((COALESCE(skor_level_3_alumni, 0) + COALESCE(skor_level_3_atasan, 0))), 2), 0)) AS avg_skor_level_3"),
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
                ->whereYear('project.tanggal_selesai', $tahun)
                ->whereBetween('project.tanggal_selesai', [$startDate, $endDate]);

            $projects = $projectsQuery->orderByDesc('project.id');
            $avgSkorLevel3All = (clone $projectsQuery)->avg('avg_scores.avg_skor_level_3');
            $avgSkorLevel4All = (clone $projectsQuery)->avg('avg_scores.final_avg_skor_level_4');

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
                ->addColumn('avg_skor_level_3', fn($row) => '<a href="' . e(url("/hasil-evaluasi/level-3/{$row->id}")) . '"  class="btn btn-link">' . e(number_format($row->avg_skor_level_3, 2)) . '</a>')
                ->addColumn('avg_skor_level_4', fn($row) => '<a href="' . e(url("/hasil-evaluasi/level-4/{$row->id}")) . '"  class="btn btn-link">' . e(number_format($row->avg_skor_level_4, 2)) . '</a>')
                ->addColumn('kriteria_dampak_level_3', fn($row) => e($row->kriteria_dampak_level_3 ?? '-'))
                ->addColumn('kriteria_dampak_level_4', fn($row) => e($row->kriteria_dampak_level_4 ?? '-'))
                ->rawColumns(['user', 'avg_skor_level_3', 'avg_skor_level_4'])
                ->with([
                    'summary' => [
                        'avg_skor_level_3' => $avgSkorLevel3All,
                        'avg_skor_level_4' => $avgSkorLevel4All,
                    ],
                    'impact_percentage' => [
                        'level_3' => round($percentageLevel3, 2),
                        'level_4' => round($percentageLevel4, 2)
                    ]
                ])
                ->toJson();
        }

        $unitKerjaList = DB::table('project_data_sekunder')
            ->select('unit_kerja')
            ->whereNotNull('unit_kerja')
            ->where('unit_kerja', '!=', '')
            ->distinct()
            ->orderBy('unit_kerja')
            ->pluck('unit_kerja');

        // Data statistik untuk pemuatan halaman awal
        $statsQueryInitial = clone $baseProjectsQuery;
        $respondenBaseQueryInitial = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.tanggal_selesai', $tahun)
            ->whereBetween('project.tanggal_selesai', [$startDate, $endDate]);

        $jumlahProject = $statsQueryInitial->count();
        $jumlahResponden = (clone $respondenBaseQueryInitial)->count();
        $sudahAlumni = (clone $respondenBaseQueryInitial)
            ->where('project_responden.status_pengisian_kuesioner_alumni', 'sudah')
            ->count();
        $persentaseSudah = $jumlahResponden > 0 ? round(($sudahAlumni / $jumlahResponden) * 100, 2) : 0;
        $totalAtasan = (clone $respondenBaseQueryInitial)
            ->whereNotNull('project_responden.nama_atasan')
            ->count();
        $sudahAtasan = (clone $respondenBaseQueryInitial)
            ->whereNotNull('project_responden.nama_atasan')
            ->where('project_responden.status_pengisian_kuesioner_atasan', 'sudah')
            ->count();
        $persentaseSudahAtasan = $totalAtasan > 0 ? round(($sudahAtasan / $totalAtasan) * 100, 2) : 0;

        return view('dashboard', compact(
            'tahun',
            'jumlahProject',
            'jumlahResponden',
            'sudahAlumni',
            'persentaseSudah',
            'sudahAtasan',
            'persentaseSudahAtasan',
            'unitKerjaList',
            'selectedTriwulan',
            'percentageLevel3',
            'percentageLevel4',
            'impactfulLevel3',
            'impactfulLevel4',
            'totalProjects'
        ));
    }
}
