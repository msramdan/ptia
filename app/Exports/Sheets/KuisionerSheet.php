<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KuisionerSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        return DB::table('project_kuesioner')
            ->where('project_id', $this->project->id)
            ->select('remark', 'aspek', 'kriteria', 'pertanyaan')
            ->orderBy('remark')->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Target Responden',
            'Aspek',
            'Kriteria',
            'Pertanyaan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('D')->getAlignment()->setWrapText(true); // Aktifkan text wrapping untuk kolom pertanyaan
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F81BD']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 20,
            'D' => 80, // Kolom pertanyaan lebih lebar
        ];
    }

    public function title(): string
    {
        return 'Kuisioner';
    }
}
