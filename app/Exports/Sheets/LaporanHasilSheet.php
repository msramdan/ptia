<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanHasilSheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
{
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Query ini diambil langsung dari HasilEvaluasiController.php untuk memastikan konsistensi data
        $hasil = DB::table('project')
            ->select(
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
            ->leftJoin('indikator_dampak AS indikator_3', function ($join) {
                $join->on('project.diklat_type_id', '=', 'indikator_3.diklat_type_id')
                    ->whereRaw('COALESCE(avg_scores.avg_skor_level_3, 0) > indikator_3.nilai_minimal AND COALESCE(avg_scores.avg_skor_level_3, 0) <= indikator_3.nilai_maksimal');
            })
            ->leftJoin('indikator_dampak AS indikator_4', function ($join) {
                $join->on('project.diklat_type_id', '=', 'indikator_4.diklat_type_id')
                    ->whereRaw('COALESCE(avg_scores.final_avg_skor_level_4, 0) > indikator_4.nilai_minimal AND COALESCE(avg_scores.final_avg_skor_level_4, 0) <= indikator_4.nilai_maksimal');
            })
            ->where('project.id', $this->project->id)
            ->first();

        // Mengubah format skor menjadi dua angka di belakang koma
        $skor_level_3 = optional($hasil)->avg_skor_level_3 ? number_format($hasil->avg_skor_level_3, 2) : '0.00';
        $skor_level_4 = optional($hasil)->avg_skor_level_4 ? number_format($hasil->avg_skor_level_4, 2) : '0.00';

        return collect([
            ['Skor Level 3', $skor_level_3],
            ['Kriteria Dampak Level 3', optional($hasil)->kriteria_dampak_level_3],
            ['Skor Level 4', $skor_level_4],
            ['Kriteria Dampak Level 4', optional($hasil)->kriteria_dampak_level_4],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:A4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F81BD']],
        ]);
        $sheet->getStyle('A1:B4')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        return [];
    }

    public function title(): string
    {
        return 'Laporan Hasil';
    }
}
