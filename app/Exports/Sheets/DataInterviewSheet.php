<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DataInterviewSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting
{
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        $data = DB::table('project_responden')
            ->where('project_id', $this->project->id)
            ->select('nama', 'nip', 'jabatan', 'unit', 'hasil_intervie_alumni', 'hasil_intervie_atasan')
            ->get();

        return $data->map(function ($item) {
            $item->hasil_intervie_alumni = $item->hasil_intervie_alumni ? strip_tags($item->hasil_intervie_alumni) : '-';
            $item->hasil_intervie_atasan = $item->hasil_intervie_atasan ? strip_tags($item->hasil_intervie_atasan) : '-';
            return $item;
        });
    }

    public function headings(): array
    {
        return ['Nama', 'NIP', 'Jabatan', 'Unit Kerja', 'Hasil Interview Alumni', 'Hasil Interview Atasan'];
    }

    public function title(): string
    {
        return 'Data Interview';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('E:F')->getAlignment()->setWrapText(true);
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F81BD']]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 35, 'B' => 25, 'C' => 45, 'D' => 45, 'E' => 60, 'F' => 60];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER, // Format NIP sebagai angka
        ];
    }
}
