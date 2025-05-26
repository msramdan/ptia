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

        // Logika untuk menghitung statistik
        $statsQuery = DB::table('project')
            ->where('status', 'Pelaksanaan')
            ->whereYear('tanggal_selesai', $tahun);

        $respondenBaseQuery = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.tanggal_selesai', $tahun);

        if ($selectedTriwulan && $selectedTriwulan != 'all') {
            $startDate = Carbon::createFromDate($tahun, ($selectedTriwulan - 1) * 3 + 1, 1)->startOfQuarter();
            $endDate = Carbon::createFromDate($tahun, ($selectedTriwulan - 1) * 3 + 1, 1)->endOfQuarter();

            $statsQuery->whereBetween('project.tanggal_selesai', [$startDate, $endDate]);
            $respondenBaseQuery->whereBetween('project.tanggal_selesai', [$startDate, $endDate]);
        }

        // Clone query untuk statistik spesifik
        $jumlahProject = (clone $statsQuery)->count();
        $jumlahResponden = (clone $respondenBaseQuery)->count();

        $sudahAlumniQuery = (clone $respondenBaseQuery)
            ->where('project_responden.status_pengisian_kuesioner_alumni', 'sudah');
        $sudahAlumni = $sudahAlumniQuery->count();
        $persentaseSudah = $jumlahResponden > 0 ? round(($sudahAlumni / $jumlahResponden) * 100, 2) : 0;

        $totalAtasanQuery = (clone $respondenBaseQuery)
            ->whereNotNull('project_responden.nama_atasan');
        $totalAtasan = $totalAtasanQuery->count();

        $sudahAtasanQuery = (clone $respondenBaseQuery)
            ->whereNotNull('project_responden.nama_atasan')
            ->where('project_responden.status_pengisian_kuesioner_atasan', 'sudah');
        $sudahAtasan = $sudahAtasanQuery->count();
        // Perbaikan: Persentase keterisian atasan seharusnya dibagi dengan total responden yang memiliki atasan,
        // atau jika dibagi dengan alumni yang sudah mengisi, pastikan logikanya sesuai.
        // Jika $sudahAlumni adalah jumlah alumni yang sudah mengisi dan menjadi basis untuk atasan, maka $totalAtasan yang relevan.
        // Atau jika ingin membandingkan dengan total alumni yang memiliki atasan:
        $persentaseSudahAtasan = $totalAtasan > 0 ? round(($sudahAtasan / $totalAtasan) * 100, 2) : 0;
        // Jika ingin membandingkan dengan jumlah alumni yang sudah mengisi:
        // $persentaseSudahAtasan = $sudahAlumni > 0 ? round(($sudahAtasan / $sudahAlumni) * 100, 2) : 0; // Pilih salah satu logika yang paling sesuai

        if ($request->ajax() && !$request->has('draw')) { // Tambahkan cek !$request->has('draw') untuk membedakan dari DataTables
            return response()->json([
                'jumlahProject' => $jumlahProject,
                'jumlahResponden' => $jumlahResponden,
                'sudahAlumni' => $sudahAlumni,
                'persentaseSudah' => $persentaseSudah,
                'sudahAtasan' => $sudahAtasan,
                'persentaseSudahAtasan' => $persentaseSudahAtasan,
                'summary' => [ // Pastikan summary juga dikirim untuk chart jika diperlukan
                    'avg_skor_level_3' => (clone $statsQuery)->leftJoinSub( /* ... subquery avg_scores ... */DB::table('project_skor_responden')
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
                        ->groupBy('project_skor_responden.project_id'), 'avg_scores', 'project.id', '=', 'avg_scores.project_id')
                        ->avg('avg_scores.avg_skor_level_3'),
                    'avg_skor_level_4' => (clone $statsQuery)->leftJoinSub( /* ... subquery avg_scores ... */DB::table('project_skor_responden')
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
                        ->groupBy('project_skor_responden.project_id'), 'avg_scores', 'project.id', '=', 'avg_scores.project_id')
                        ->avg('avg_scores.final_avg_skor_level_4'),
                ]
            ]);
        }

        if (request()->ajax() && $request->has('draw')) { // Ini adalah request DataTables
            $projectsQuery = DB::table('project')
                ->select(
                    'project.id',
                    // 'project.kode_project', // tidak ada di view
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
                ->whereYear('project.tanggal_selesai', $tahun);

            if ($selectedTriwulan && $selectedTriwulan != 'all') {
                $startDate = Carbon::createFromDate($tahun, ($selectedTriwulan - 1) * 3 + 1, 1)->startOfQuarter();
                $endDate = Carbon::createFromDate($tahun, ($selectedTriwulan - 1) * 3 + 1, 1)->endOfQuarter();
                $projectsQuery->whereBetween('project.tanggal_selesai', [$startDate, $endDate]);
            }

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
                        'count' => $projects->count() // Mungkin tidak perlu jika sudah dihandle di luar
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


        return view('dashboard', compact(
            'tahun',
            'jumlahProject',
            'jumlahResponden',
            'sudahAlumni',
            'persentaseSudah',
            'sudahAtasan',
            'persentaseSudahAtasan',
            'unitKerjaList',
            'selectedTriwulan'
        ));
    }
}
