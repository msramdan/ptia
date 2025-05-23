<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use App\Exports\RekapHasilEvaluasiExport;
use Maatwebsite\Excel\Facades\Excel;


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

    public function index(Request $request): View|JsonResponse
    {
        if (request()->ajax()) {
            $projectsQuery = DB::table('project')
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
                ->where('project.status', 'Pelaksanaan')
                ->when($request->evaluator, function ($query, $evaluator) {
                    $query->where('project.user_id', $evaluator);
                })
                ->when($request->diklat_type, function ($query, $diklatType) {
                    $query->where('project.diklat_type_id', $diklatType);
                });

            $projects = $projectsQuery->orderByDesc('project.id');

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
                ->addColumn('avg_skor_level_3', fn($row) => '<a href="' . e(url("/hasil-evaluasi/level-3/{$row->id}")) . '" class="btn btn-link">' . e(number_format($row->avg_skor_level_3, 2)) . '</a>')
                ->addColumn('avg_skor_level_4', fn($row) => '<a href="' . e(url("/hasil-evaluasi/level-4/{$row->id}")) . '" class="btn btn-link">' . e(number_format($row->avg_skor_level_4, 2)) . '</a>')
                ->addColumn('kriteria_dampak_level_3', fn($row) => e($row->kriteria_dampak_level_3 ?? '-'))
                ->addColumn('kriteria_dampak_level_4', fn($row) => e($row->kriteria_dampak_level_4 ?? '-'))
                ->rawColumns(['user', 'avg_skor_level_3', 'avg_skor_level_4'])
                ->toJson();
        }

        $unitKerjaList = DB::table('project_data_sekunder')
            ->select('unit_kerja')
            ->whereNotNull('unit_kerja')
            ->where('unit_kerja', '!=', '')
            ->distinct()
            ->orderBy('unit_kerja')
            ->pluck('unit_kerja');

        $evaluators = DB::table('users')
            ->select('id', 'name')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('project')
                    ->whereColumn('project.user_id', 'users.id');
            })
            ->orderBy('name')
            ->get();

        $diklatTypes = DB::table('diklat_type')
            ->select('id', 'nama_diklat_type')
            ->orderBy('nama_diklat_type')
            ->get();

        return view('hasil-evaluasi.index', compact('unitKerjaList', 'evaluators', 'diklatTypes'));
    }

    public function showLevel3($id)
    {
        $project = DB::table('project')
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();
        if (!$project) {
            abort(404, 'Project tidak ditemukan');
        }

        if (request()->ajax()) {
            $data = DB::table('project_skor_responden')
                ->join('project_responden', 'project_responden.id', '=', 'project_skor_responden.project_responden_id')
                ->join('project', 'project.id', '=', 'project_skor_responden.project_id')
                ->join('indikator_dampak', function ($join) {
                    $join->on('indikator_dampak.diklat_type_id', '=', 'project.diklat_type_id')
                        ->whereRaw('
                        (COALESCE(project_skor_responden.skor_level_3_alumni, 0) +
                         COALESCE(project_skor_responden.skor_level_3_atasan, 0))
                        > indikator_dampak.nilai_minimal
                        AND
                        (COALESCE(project_skor_responden.skor_level_3_alumni, 0) +
                         COALESCE(project_skor_responden.skor_level_3_atasan, 0))
                        <= indikator_dampak.nilai_maksimal
                    ');
                })
                ->where('project_skor_responden.project_id', $id)
                ->select([
                    'project_skor_responden.project_id',
                    'project_skor_responden.project_responden_id',
                    'project_skor_responden.skor_level_3_alumni',
                    'project_skor_responden.skor_level_3_atasan',
                    'project_responden.nama',
                    'project_responden.nip',
                    'project_responden.telepon',
                    'project_responden.jabatan',
                    'project_responden.unit',
                    'project.diklat_type_id',
                    DB::raw('
                    LEAST(100, ROUND(
                        (COALESCE(project_skor_responden.skor_level_3_alumni, 0) +
                         COALESCE(project_skor_responden.skor_level_3_atasan, 0)),
                        2
                    )) AS avg_skor_level_3
                '),
                    'indikator_dampak.kriteria_dampak'
                ])
                ->get();

            // Hitung rata-rata dari semua `avg_skor_level_3`
            $average = $data->avg('avg_skor_level_3');

            return DataTables::of($data)
                ->with('average_skor_level_3', round($average, 2)) // Kirim rata-rata ke frontend
                ->addIndexColumn()
                ->toJson();
        }

        return view('hasil-evaluasi.detail-skor-level3', compact('project'));
    }


    public function getDetailSkorLevel3(Request $request)
    {

        $respondenId = $request->query('project_responden_id');

        $data = DB::select("WITH delta_data AS (
                SELECT
                    project_jawaban_kuesioner.remark,
                    project_kuesioner.aspek_id,
                    project_kuesioner.level,
                    project_kuesioner.aspek,
                    project_kuesioner.kriteria,
                    project.diklat_type_id,
                    project.id AS project_id,
                    ROUND(AVG(project_jawaban_kuesioner.nilai_delta), 0) AS rata_rata_delta
                FROM project_jawaban_kuesioner
                JOIN project_kuesioner
                    ON project_jawaban_kuesioner.project_kuesioner_id = project_kuesioner.id
                JOIN project
                    ON project_kuesioner.project_id = project.id
                WHERE
                    project_jawaban_kuesioner.project_responden_id = ?
                    AND project_kuesioner.level = '3'
                GROUP BY
                    project_kuesioner.aspek_id,
                    project_jawaban_kuesioner.remark,
                    project_kuesioner.level,
                    project_kuesioner.aspek,
                    project_kuesioner.kriteria,
                    project.diklat_type_id,
                    project.id
            )
            SELECT
                delta_data.remark,
                delta_data.aspek_id,
                delta_data.level,
                delta_data.aspek,
                delta_data.kriteria,
                delta_data.diklat_type_id,
                delta_data.rata_rata_delta,
                konversi.konversi,  -- Menambahkan nilai konversi
                -- Ambil bobot berdasarkan remark
                CASE
                    WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                    WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                    ELSE NULL
                END AS bobot,
                -- Perhitungan nilai = (konversi * bobot) / 100, dibulatkan 2 angka desimal
                ROUND(
                    CASE
                        WHEN konversi.konversi IS NOT NULL AND
                            (delta_data.remark = 'Alumni' OR delta_data.remark = 'Atasan')
                        THEN (konversi.konversi *
                            CASE
                                WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                                WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                                ELSE 0
                            END) / 100
                        ELSE NULL
                    END, 2
                ) AS nilai
            FROM delta_data
            LEFT JOIN konversi
                ON delta_data.diklat_type_id = konversi.diklat_type_id
                AND delta_data.rata_rata_delta = konversi.skor
                AND (
                    (delta_data.kriteria = 'Skor Persepsi' AND konversi.jenis_skor = 'Skor Persepsi')
                    OR
                    (delta_data.kriteria = 'Delta Skor Persepsi' AND konversi.jenis_skor = '∆ Skor Persepsi')
                )
            LEFT JOIN project_bobot_aspek
                ON delta_data.project_id = project_bobot_aspek.project_id
                AND delta_data.aspek_id = project_bobot_aspek.aspek_id", [$respondenId]);

        $groupedData = [];

        foreach ($data as $item) {
            $groupedData[$item->remark][] = $item;
        }

        $totalAlumni = 0;
        $totalAtasan = 0;
        $countAlumni = 0;
        $countAtasan = 0;

        $html = '<style>
        #skorModal table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }
        #skorModal th, #skorModal td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        #skorModal th {
            background-color: #f2f2f2;
        }
        #skorModal .group-header {
            background-color: #d1e7dd;
            font-weight: bold;
        }
        #skorModal .total-row {
            background-color: #f8d7da;
            font-weight: bold;
        }
        #skorModal .avg-row {
            background-color: #d4e3fc;
            font-weight: bold;
        }
    </style>';

        $html .= '<table>';
        $html .= '<tr>
            <th>Aspek</th>
            <th>Kriteria</th>
            <th>Rata-rata Skor</th>
            <th>Konversi</th>
            <th>Bobot</th>
            <th>Nilai</th>
         </tr>';

        foreach ($groupedData as $remark => $items) {
            $html .= "<tr class='group-header'><td colspan='6'>$remark</td></tr>";

            $totalNilai = 0;
            $count = 0;

            foreach ($items as $item) {
                $html .= "<tr>
                    <td style='text-align: left !important;'>{$item->aspek}</td>
                    <td>{$item->kriteria}</td>
                    <td>{$item->rata_rata_delta}</td>
                    <td>{$item->konversi}</td>
                    <td>{$item->bobot}</td>
                    <td>{$item->nilai}</td>
                  </tr>";

                $totalNilai += $item->nilai;
                $count++;

                if ($remark == 'Alumni') {
                    $totalAlumni += $item->nilai;
                    $countAlumni++;
                } else {
                    $totalAtasan += $item->nilai;
                    $countAtasan++;
                }
            }

            $html .= "<tr class='total-row'>
                <td colspan='5'>Sub Total Nilai</td>
                <td>$totalNilai</td>
              </tr>";
        }

        $avgNilai = round(($totalAlumni + $totalAtasan), 2);

        $html .= "<tr class='avg-row'>
            <td colspan='5'>Total Nilai</td>
            <td>$avgNilai</td>
          </tr>";

        $html .= '</table>';

        echo $html;
    }

    public function showLevel4($id)
    {
        $project = DB::table('project')
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();

        if (!$project) {
            abort(404, 'Project tidak ditemukan');
        }

        // Ambil data sekunder untuk perbandingan nilai kinerja awal dan akhir
        $dataSekunder = DB::table('project_data_sekunder')
            ->where('project_id', $id)
            ->select('nilai_kinerja_awal', 'nilai_kinerja_akhir')
            ->first();

        $bobotAspekSekunder = 0;

        if ($dataSekunder && $dataSekunder->nilai_kinerja_akhir > $dataSekunder->nilai_kinerja_awal) {
            // Jika ada kenaikan, ambil bobot_aspek_sekunder dari project_bobot_aspek_sekunder
            $bobot = DB::table('project_bobot_aspek_sekunder')
                ->where('project_id', $id)
                ->value('bobot_aspek_sekunder');

            $bobotAspekSekunder = $bobot ?? 0;
        }

        if (request()->ajax()) {
            $data = DB::table('project_skor_responden')
                ->join('project_responden', 'project_responden.id', '=', 'project_skor_responden.project_responden_id')
                ->join('project', 'project.id', '=', 'project_skor_responden.project_id')
                ->join('indikator_dampak', function ($join) use ($bobotAspekSekunder) {
                    $join->on('indikator_dampak.diklat_type_id', '=', 'project.diklat_type_id')
                        ->whereRaw('
                            (COALESCE(project_skor_responden.skor_level_4_alumni, 0) +
                             COALESCE(project_skor_responden.skor_level_4_atasan, 0) +
                             ?) > indikator_dampak.nilai_minimal
                            AND
                            LEAST(
                                (COALESCE(project_skor_responden.skor_level_4_alumni, 0) +
                                 COALESCE(project_skor_responden.skor_level_4_atasan, 0) +
                                 ?), 100
                            ) <= indikator_dampak.nilai_maksimal
                        ', [$bobotAspekSekunder, $bobotAspekSekunder]);
                })
                ->where('project_skor_responden.project_id', $id)
                ->select([
                    'project_skor_responden.project_id',
                    'project_skor_responden.project_responden_id',
                    'project_skor_responden.skor_level_4_alumni',
                    'project_skor_responden.skor_level_4_atasan',
                    'project_responden.nama',
                    'project_responden.nip',
                    'project_responden.telepon',
                    'project_responden.jabatan',
                    'project_responden.unit',
                    'project.diklat_type_id',
                    DB::raw('
                    ROUND(
                        (COALESCE(project_skor_responden.skor_level_4_alumni, 0) +
                         COALESCE(project_skor_responden.skor_level_4_atasan, 0) +
                         ' . $bobotAspekSekunder . '),
                        2
                    ) AS avg_skor_level_4
                '),
                    'indikator_dampak.kriteria_dampak'
                ])
                ->get();
            // Hitung rata-rata dari semua `avg_skor_level_3`
            $average = $data->avg('avg_skor_level_4');
            return DataTables::of($data)
                ->with('average_skor_level_4', round($average, 2)) // Kirim rata-rata ke frontend
                ->addIndexColumn()
                ->toJson();

            return DataTables::of($data)->addIndexColumn()->toJson();
        }

        return view('hasil-evaluasi.detail-skor-level4', compact('project'));
    }

    public function getDetailSkorLevel4(Request $request)
    {

        $respondenId = $request->query('project_responden_id');
        $project_id = $request->query('project_id');
        // Ambil data sekunder untuk perbandingan nilai kinerja awal dan akhir
        $dataSekunder = DB::table('project_data_sekunder')
            ->where('project_id', $project_id)
            ->select('nilai_kinerja_awal', 'nilai_kinerja_akhir')
            ->first();

        $bobotAspekSekunder = 0;

        if ($dataSekunder && $dataSekunder->nilai_kinerja_akhir > $dataSekunder->nilai_kinerja_awal) {
            // Jika ada kenaikan, ambil bobot_aspek_sekunder dari project_bobot_aspek_sekunder
            $bobot = DB::table('project_bobot_aspek_sekunder')
                ->where('project_id', $project_id)
                ->value('bobot_aspek_sekunder');

            $bobotAspekSekunder = $bobot ?? 0;
        }

        $data = DB::select("WITH delta_data AS (
                SELECT
                    project_jawaban_kuesioner.remark,
                    project_kuesioner.aspek_id,
                    project_kuesioner.level,
                    project_kuesioner.aspek,
                    project_kuesioner.kriteria,
                    project.diklat_type_id,
                    project.id AS project_id,
                    ROUND(AVG(project_jawaban_kuesioner.nilai_delta), 0) AS rata_rata_delta
                FROM project_jawaban_kuesioner
                JOIN project_kuesioner
                    ON project_jawaban_kuesioner.project_kuesioner_id = project_kuesioner.id
                JOIN project
                    ON project_kuesioner.project_id = project.id
                WHERE
                    project_jawaban_kuesioner.project_responden_id = ?
                    AND project_kuesioner.level = '4'
                GROUP BY
                    project_kuesioner.aspek_id,
                    project_jawaban_kuesioner.remark,
                    project_kuesioner.level,
                    project_kuesioner.aspek,
                    project_kuesioner.kriteria,
                    project.diklat_type_id,
                    project.id
            )
            SELECT
                delta_data.remark,
                delta_data.aspek_id,
                delta_data.level,
                delta_data.aspek,
                delta_data.kriteria,
                delta_data.diklat_type_id,
                delta_data.rata_rata_delta,
                konversi.konversi,  -- Menambahkan nilai konversi
                -- Ambil bobot berdasarkan remark
                CASE
                    WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                    WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                    ELSE NULL
                END AS bobot,
                -- Perhitungan nilai = (konversi * bobot) / 100, dibulatkan 2 angka desimal
                ROUND(
                    CASE
                        WHEN konversi.konversi IS NOT NULL AND
                            (delta_data.remark = 'Alumni' OR delta_data.remark = 'Atasan')
                        THEN (konversi.konversi *
                            CASE
                                WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                                WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                                ELSE 0
                            END) / 100
                        ELSE NULL
                    END, 2
                ) AS nilai
            FROM delta_data
            LEFT JOIN konversi
                ON delta_data.diklat_type_id = konversi.diklat_type_id
                AND delta_data.rata_rata_delta = konversi.skor
                AND (
                    (delta_data.kriteria = 'Skor Persepsi' AND konversi.jenis_skor = 'Skor Persepsi')
                    OR
                    (delta_data.kriteria = 'Delta Skor Persepsi' AND konversi.jenis_skor = '∆ Skor Persepsi')
                )
            LEFT JOIN project_bobot_aspek
                ON delta_data.project_id = project_bobot_aspek.project_id
                AND delta_data.aspek_id = project_bobot_aspek.aspek_id", [$respondenId]);

        $groupedData = [];

        foreach ($data as $item) {
            $groupedData[$item->remark][] = $item;
        }

        $totalAlumni = 0;
        $totalAtasan = 0;
        $countAlumni = 0;
        $countAtasan = 0;

        $html = '<style>
        #skorModal table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }
        #skorModal th, #skorModal td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        #skorModal th {
            background-color: #f2f2f2;
        }
        #skorModal .group-header {
            background-color: #d1e7dd;
            font-weight: bold;
        }
        #skorModal .total-row {
            background-color: #f8d7da;
            font-weight: bold;
        }
        #skorModal .avg-row {
            background-color: #d4e3fc;
            font-weight: bold;
        }
    </style>';

        $html .= '<table>';
        $html .= '<tr>
            <th>Aspek</th>
            <th>Kriteria</th>
            <th>Rata-rata Skor</th>
            <th>Konversi</th>
            <th>Bobot</th>
            <th>Nilai</th>
         </tr>';

        foreach ($groupedData as $remark => $items) {
            $html .= "<tr class='group-header'><td colspan='6'>$remark</td></tr>";

            $totalNilai = 0;
            $count = 0;

            foreach ($items as $item) {
                $html .= "<tr>
                    <td style='text-align: left !important;'>{$item->aspek}</td>
                    <td>{$item->kriteria}</td>
                    <td>{$item->rata_rata_delta}</td>
                    <td>{$item->konversi}</td>
                    <td>{$item->bobot}</td>
                    <td>{$item->nilai}</td>
                  </tr>";

                $totalNilai += $item->nilai;
                $count++;

                if ($remark == 'Alumni') {
                    $totalAlumni += $item->nilai;
                    $countAlumni++;
                } else {
                    $totalAtasan += $item->nilai;
                    $countAtasan++;
                }
            }

            $html .= "<tr class='total-row'>
                <td colspan='5'>Sub Total Nilai</td>
                <td>$totalNilai</td>
              </tr>";
        }

        $html .= "<tr class='total-row'>
        <td colspan='5'>Data Sekunder</td>
        <td>$bobotAspekSekunder</td>
      </tr>";

        $total = round(($totalAlumni + $totalAtasan + $bobotAspekSekunder), 2);

        $html .= "<tr class='avg-row'>
            <td colspan='5'>Total Nilai</td>
            <td>$total</td>
          </tr>";

        $html .= '</table>';

        echo $html;
    }

    public function exportExcel()
    {
        return Excel::download(new RekapHasilEvaluasiExport(), 'rekap-hasil-evaluasi.xlsx');
    }
}
