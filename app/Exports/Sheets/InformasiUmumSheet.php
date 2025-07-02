<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InformasiUmumSheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
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
        return collect([
            ['Kode Diklat', $this->project->kaldikID],
            ['Nama Diklat', $this->project->kaldikDesc],
            ['Evaluator', optional($this->project->user)->name],
            ['Tanggal Selesai', $this->project->tanggal_selesai]
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Mendapatkan baris terakhir yang berisi data
        $lastRow = $sheet->getHighestRow();

        // Style untuk kolom pertama (A) sebagai header
        $sheet->getStyle('A1:A' . $lastRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => 'FF4F81BD'],
            ],
        ]);

        // Menambahkan border ke semua sel yang berisi data (A1 sampai B dan baris terakhir)
        $sheet->getStyle('A1:B' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Menambahkan style untuk baris pertama kolom B agar rata kiri
        $sheet->getStyle('B1')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Informasi Umum Project';
    }
}
