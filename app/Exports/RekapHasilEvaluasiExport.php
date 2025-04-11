<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;

class RekapHasilEvaluasiExport implements FromView, ShouldAutoSize, WithEvents, WithTitle
{

    public function title(): string
    {
        return 'Rekap Hasil Evaluasi ';
    }

    public function view(): View
    {
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
            ->orderByDesc('project.id')->get();

        return view('hasil-evaluasi.export', [
            'projects' => $projects,
        ]);
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:I2';
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }
}
