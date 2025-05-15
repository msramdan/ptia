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
        $selectedUnitKerja = $request->input('unit_kerja');
        $selectedTriwulan = $request->input('triwulan'); // Ambil input triwulan

        $unitKerjaList = DB::table('project_data_sekunder')
            ->select('unit_kerja')
            ->whereNotNull('unit_kerja')
            ->where('unit_kerja', '!=', '')
            ->distinct()
            ->orderBy('unit_kerja')
            ->pluck('unit_kerja');

        if (request()->ajax()) {
            $projectsQuery = DB::table('project')
                ->select( /* ... kolom select seperti sebelumnya ... */
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
                    DB::raw("COALESCE(indikator_4.kriteria_dampak, '-') AS kriteria_dampak_level_4"),
                    'project.created_at' // Pastikan created_at ada untuk filter triwulan
                )
                ->leftJoinSub( /* ... leftJoinSub avg_scores ... */
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
                ->leftJoin('indikator_dampak AS indikator_3', /* ... join indikator_3 ... */ function ($join) {
                    $join->on('project.diklat_type_id', '=', 'indikator_3.diklat_type_id')
                        ->whereRaw('
                        COALESCE(avg_scores.avg_skor_level_3, 0) > indikator_3.nilai_minimal
                        AND
                        COALESCE(avg_scores.avg_skor_level_3, 0) <= indikator_3.nilai_maksimal
                    ');
                })
                ->leftJoin('indikator_dampak AS indikator_4', /* ... join indikator_4 ... */ function ($join) {
                    $join->on('project.diklat_type_id', '=', 'indikator_4.diklat_type_id')
                        ->whereRaw('
                        COALESCE(avg_scores.final_avg_skor_level_4, 0) > indikator_4.nilai_minimal
                        AND
                        COALESCE(avg_scores.final_avg_skor_level_4, 0) <= indikator_4.nilai_maksimal
                    ');
                })
                ->where('project.status', 'Pelaksanaan')
                ->whereYear('project.created_at', $tahun);

            // Filter Triwulan
            if ($selectedTriwulan && $selectedTriwulan != 'all') {
                $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
                $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfDay();

                switch ($selectedTriwulan) {
                    case '1': // Triwulan 1 (1 Jan - Akhir Maret)
                        $endDate = Carbon::createFromDate($tahun, 3, 1)->endOfMonth()->endOfDay();
                        break;
                    case '2': // Triwulan 2 (1 Jan - Akhir Juni)
                        $endDate = Carbon::createFromDate($tahun, 6, 1)->endOfMonth()->endOfDay();
                        break;
                    case '3': // Triwulan 3 (1 Jan - Akhir September)
                        $endDate = Carbon::createFromDate($tahun, 9, 1)->endOfMonth()->endOfDay();
                        break;
                    case '4': // Triwulan 4 (1 Jan - Akhir Desember)
                        $endDate = Carbon::createFromDate($tahun, 12, 1)->endOfMonth()->endOfDay();
                        break;
                }
                $projectsQuery->whereBetween('project.created_at', [$startDate, $endDate]);
            }


            if ($selectedUnitKerja) {
                $projectsQuery->whereIn('project.id', function ($query) use ($selectedUnitKerja) {
                    $query->select('project_id')
                        ->from('project_data_sekunder')
                        ->where('unit_kerja', $selectedUnitKerja);
                });
            }
            $projects = $projectsQuery->orderByDesc('project.id');

            return DataTables::of($projects)
                /* ... kolom DataTables seperti sebelumnya ... */
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
                    'summary' => [ // Perlu dihitung ulang berdasarkan filter triwulan juga
                        'avg_skor_level_3' => $projects->avg('avg_scores.avg_skor_level_3'),
                        'avg_skor_level_4' => $projects->avg('avg_scores.final_avg_skor_level_4'),
                        'count' => $projects->count()
                    ]
                ])
                ->toJson();
        }

        // Hitung ulang summary stats dengan filter unit kerja dan triwulan jika ada
        $statsQuery = DB::table('project')
            ->where('status', 'Pelaksanaan')
            ->whereYear('created_at', $tahun);

        if ($selectedUnitKerja) {
            $statsQuery->whereIn('id', function ($query) use ($selectedUnitKerja) {
                $query->select('project_id')
                    ->from('project_data_sekunder')
                    ->where('unit_kerja', $selectedUnitKerja);
            });
        }
        // Filter Triwulan untuk stats
        if ($selectedTriwulan && $selectedTriwulan != 'all') {
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfDay(); // Default untuk all
            switch ($selectedTriwulan) {
                case '1':
                    $endDate = Carbon::createFromDate($tahun, 3, 1)->endOfMonth()->endOfDay();
                    break;
                case '2':
                    $endDate = Carbon::createFromDate($tahun, 6, 1)->endOfMonth()->endOfDay();
                    break;
                case '3':
                    $endDate = Carbon::createFromDate($tahun, 9, 1)->endOfMonth()->endOfDay();
                    break;
                case '4':
                    $endDate = Carbon::createFromDate($tahun, 12, 1)->endOfMonth()->endOfDay();
                    break;
            }
            $statsQuery->whereBetween('project.created_at', [$startDate, $endDate]);
        }
        $jumlahProject = $statsQuery->count();


        $respondenQuery = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.created_at', $tahun);
        if ($selectedUnitKerja) {
            $respondenQuery->whereIn('project_responden.project_id', function ($query) use ($selectedUnitKerja) {
                $query->select('project_id')
                    ->from('project_data_sekunder')
                    ->where('unit_kerja', $selectedUnitKerja);
            });
        }
        if ($selectedTriwulan && $selectedTriwulan != 'all') {
            // (Logika filter triwulan yang sama untuk respondenQuery)
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfDay();
            switch ($selectedTriwulan) {
                case '1':
                    $endDate = Carbon::createFromDate($tahun, 3, 1)->endOfMonth()->endOfDay();
                    break;
                case '2':
                    $endDate = Carbon::createFromDate($tahun, 6, 1)->endOfMonth()->endOfDay();
                    break;
                case '3':
                    $endDate = Carbon::createFromDate($tahun, 9, 1)->endOfMonth()->endOfDay();
                    break;
                case '4':
                    $endDate = Carbon::createFromDate($tahun, 12, 1)->endOfMonth()->endOfDay();
                    break;
            }
            $respondenQuery->whereBetween('project.created_at', [$startDate, $endDate]);
        }
        $jumlahResponden = $respondenQuery->count();

        // (Lanjutkan pola filter yang sama untuk $sudahAlumniQuery, $totalAtasanQuery, $sudahAtasanQuery)
        $sudahAlumniQuery = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.created_at', $tahun)
            ->where('project_responden.status_pengisian_kuesioner_alumni', 'sudah');
        if ($selectedUnitKerja) { /* ... filter unit kerja ... */
            $sudahAlumniQuery->whereIn('project_responden.project_id', function ($query) use ($selectedUnitKerja) {
                $query->select('project_id')
                    ->from('project_data_sekunder')
                    ->where('unit_kerja', $selectedUnitKerja);
            });
        }
        if ($selectedTriwulan && $selectedTriwulan != 'all') { /* ... filter triwulan ... */
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfDay();
            switch ($selectedTriwulan) {
                case '1':
                    $endDate = Carbon::createFromDate($tahun, 3, 1)->endOfMonth()->endOfDay();
                    break;
                case '2':
                    $endDate = Carbon::createFromDate($tahun, 6, 1)->endOfMonth()->endOfDay();
                    break;
                case '3':
                    $endDate = Carbon::createFromDate($tahun, 9, 1)->endOfMonth()->endOfDay();
                    break;
                case '4':
                    $endDate = Carbon::createFromDate($tahun, 12, 1)->endOfMonth()->endOfDay();
                    break;
            }
            $sudahAlumniQuery->whereBetween('project.created_at', [$startDate, $endDate]);
        }
        $sudahAlumni = $sudahAlumniQuery->count();
        $persentaseSudah = $jumlahResponden > 0 ? round(($sudahAlumni / $jumlahResponden) * 100, 2) : 0;

        $totalAtasanQuery = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.created_at', $tahun)
            ->whereNotNull('project_responden.nama_atasan');
        if ($selectedUnitKerja) { /* ... filter unit kerja ... */
            $totalAtasanQuery->whereIn('project_responden.project_id', function ($query) use ($selectedUnitKerja) {
                $query->select('project_id')
                    ->from('project_data_sekunder')
                    ->where('unit_kerja', $selectedUnitKerja);
            });
        }
        if ($selectedTriwulan && $selectedTriwulan != 'all') { /* ... filter triwulan ... */
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfDay();
            switch ($selectedTriwulan) {
                case '1':
                    $endDate = Carbon::createFromDate($tahun, 3, 1)->endOfMonth()->endOfDay();
                    break;
                case '2':
                    $endDate = Carbon::createFromDate($tahun, 6, 1)->endOfMonth()->endOfDay();
                    break;
                case '3':
                    $endDate = Carbon::createFromDate($tahun, 9, 1)->endOfMonth()->endOfDay();
                    break;
                case '4':
                    $endDate = Carbon::createFromDate($tahun, 12, 1)->endOfMonth()->endOfDay();
                    break;
            }
            $totalAtasanQuery->whereBetween('project.created_at', [$startDate, $endDate]);
        }
        $totalAtasan = $totalAtasanQuery->count();

        $sudahAtasanQuery = DB::table('project_responden')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->whereYear('project.created_at', $tahun)
            ->whereNotNull('project_responden.nama_atasan')
            ->where('project_responden.status_pengisian_kuesioner_atasan', 'sudah');
        if ($selectedUnitKerja) { /* ... filter unit kerja ... */
            $sudahAtasanQuery->whereIn('project_responden.project_id', function ($query) use ($selectedUnitKerja) {
                $query->select('project_id')
                    ->from('project_data_sekunder')
                    ->where('unit_kerja', $selectedUnitKerja);
            });
        }
        if ($selectedTriwulan && $selectedTriwulan != 'all') { /* ... filter triwulan ... */
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfDay();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfDay();
            switch ($selectedTriwulan) {
                case '1':
                    $endDate = Carbon::createFromDate($tahun, 3, 1)->endOfMonth()->endOfDay();
                    break;
                case '2':
                    $endDate = Carbon::createFromDate($tahun, 6, 1)->endOfMonth()->endOfDay();
                    break;
                case '3':
                    $endDate = Carbon::createFromDate($tahun, 9, 1)->endOfMonth()->endOfDay();
                    break;
                case '4':
                    $endDate = Carbon::createFromDate($tahun, 12, 1)->endOfMonth()->endOfDay();
                    break;
            }
            $sudahAtasanQuery->whereBetween('project.created_at', [$startDate, $endDate]);
        }
        $sudahAtasan = $sudahAtasanQuery->count();
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
            'selectedUnitKerja',
            'selectedTriwulan' // Kirim triwulan yang dipilih ke view
        ));
    }
}
