<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DataSekunderSheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
{
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        $dataSekunder = DB::table('project_data_sekunder')
            ->where('project_id', $this->project->id)
            ->first();

        // Hanya menampilkan Kinerja Awal dan Periode Awal
        return collect([
            ['Kinerja Awal', optional($dataSekunder)->nilai_kinerja_awal],
            ['Periode Awal', optional($dataSekunder)->periode_awal],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk kolom A sebagai header
        $sheet->getStyle('A1:A2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F81BD']],
        ]);

        // Menambahkan border ke semua sel
        $sheet->getStyle('A1:B2')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Meratakan teks di kolom B (Value) menjadi rata kiri
        $sheet->getStyle('B1:B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [];
    }

    public function title(): string
    {
        return 'Data Sekunder';
    }
}
