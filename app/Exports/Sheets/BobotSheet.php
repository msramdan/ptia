<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BobotSheet implements FromCollection, WithTitle, ShouldAutoSize, WithEvents
{
    private $project;
    private $level3Data;
    private $level4Data;
    private $dataSecondary;
    private $rowCount = 0;

    public function __construct(Project $project)
    {
        $this->project = $project;

        $bobotAspek = DB::table('project_bobot_aspek')
            ->join('aspek', 'project_bobot_aspek.aspek_id', '=', 'aspek.id')
            ->select('project_bobot_aspek.*', 'aspek.aspek as aspek_nama', 'aspek.level')
            ->where('project_bobot_aspek.project_id', $this->project->id)
            ->get();

        $this->level3Data = $bobotAspek->where('level', 3);
        $this->level4Data = $bobotAspek->where('level', 4);

        $this->dataSecondary = DB::table('project_bobot_aspek_sekunder')
            ->where('project_id', $this->project->id)
            ->first();
    }

    public function collection()
    {
        $collection = collect();

        // Header Utama
        $collection->push(['Aspek', 'Bobot Alumni', 'Bobot Atasan']);

        // LEVEL 3
        $collection->push(['LEVEL 3 (Data Primer)', null, null]);

        $totalAlumniL3 = 0;
        $totalAtasanL3 = 0;
        foreach ($this->level3Data as $bobot) {
            $collection->push([$bobot->aspek_nama, $bobot->bobot_alumni, $bobot->bobot_atasan_langsung]);
            $totalAlumniL3 += $bobot->bobot_alumni;
            $totalAtasanL3 += $bobot->bobot_atasan_langsung;
        }
        $collection->push(['Sub Total', $totalAlumniL3, $totalAtasanL3]);
        $collection->push(['Total', $totalAlumniL3 + $totalAtasanL3, null]); // Perubahan di sini

        // Spacer
        $collection->push(['', '', '']);

        // LEVEL 4
        $collection->push(['LEVEL 4 (Data Primer & Sekunder)', null, null]);

        $totalAlumniL4 = 0;
        $totalAtasanL4 = 0;
        foreach ($this->level4Data as $bobot) {
            $collection->push(['Data Primer : ' . $bobot->aspek_nama, $bobot->bobot_alumni, $bobot->bobot_atasan_langsung]);
            $totalAlumniL4 += $bobot->bobot_alumni;
            $totalAtasanL4 += $bobot->bobot_atasan_langsung;
        }

        $bobotSekunder = optional($this->dataSecondary)->bobot_aspek_sekunder ?? 0;
        $collection->push(['Data Sekunder : Hasil Pelatihan', $bobotSekunder, '']);

        $totalAlumniL4 += $bobotSekunder;

        $collection->push(['Sub Total', $totalAlumniL4, $totalAtasanL4]);
        $collection->push(['Total', $totalAlumniL4 + $totalAtasanL4, null]); // Perubahan di sini

        $this->rowCount = $collection->count();
        return $collection;
    }

    public function title(): string
    {
        return 'Bobot';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:C1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // LEVEL 3
                $rowL3Header = 2;
                $sheet->mergeCells("A{$rowL3Header}:C{$rowL3Header}");
                $sheet->getStyle("A{$rowL3Header}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1A375E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $rowL3Subtotal = $rowL3Header + $this->level3Data->count() + 1;
                $rowL3Total = $rowL3Subtotal + 1;

                $sheet->getStyle("A{$rowL3Subtotal}:C{$rowL3Subtotal}")->getFont()->setBold(true);
                $sheet->getStyle("A{$rowL3Total}:B{$rowL3Total}")->getFont()->setBold(true);

                $sheet->mergeCells("B{$rowL3Total}:C{$rowL3Total}"); // Merge sel untuk total
                $sheet->getStyle("B{$rowL3Total}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $totalL3Value = $sheet->getCell("B{$rowL3Total}")->getValue();
                if ($totalL3Value != 100) {
                    $sheet->getStyle("B{$rowL3Total}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');
                    $sheet->getStyle("B{$rowL3Total}")->getFont()->getColor()->setARGB('FFFFFFFF');
                }

                // LEVEL 4
                $rowL4Header = $rowL3Total + 2;
                $sheet->mergeCells("A{$rowL4Header}:C{$rowL4Header}");
                $sheet->getStyle("A{$rowL4Header}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1A375E']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $rowL4Subtotal = $rowL4Header + $this->level4Data->count() + 1 + 1; // +1 untuk data sekunder
                $rowL4Total = $rowL4Subtotal + 1;

                $sheet->getStyle("A{$rowL4Subtotal}:C{$rowL4Subtotal}")->getFont()->setBold(true);
                $sheet->getStyle("A{$rowL4Total}:B{$rowL4Total}")->getFont()->setBold(true);

                $sheet->mergeCells("B{$rowL4Total}:C{$rowL4Total}"); // Merge sel untuk total
                $sheet->getStyle("B{$rowL4Total}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $totalL4Value = $sheet->getCell("B{$rowL4Total}")->getValue();
                if ($totalL4Value != 100) {
                    $sheet->getStyle("B{$rowL4Total}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');
                    $sheet->getStyle("B{$rowL4Total}")->getFont()->getColor()->setARGB('FFFFFFFF');
                }

                // Format persentase
                $sheet->getStyle("B2:C{$this->rowCount}")->getNumberFormat()->setFormatCode('#,##0.00"%"');
            },
        ];
    }
}
