<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DaftarRespondenSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection(): mixed
    {
        return DB::table('project_responden')
            ->where('project_id', $this->project->id)
            ->select('nama', 'nip', 'jabatan', 'unit')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIP',
            'Jabatan',
            'Unit Kerja',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F81BD']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 25, // Auto fit untuk NIP
            'C' => 45,
            'D' => 45,
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Format kolom B (NIP) sebagai teks agar tidak menjadi scientific
            'B' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function title(): string
    {
        return 'Daftar Responden';
    }
}
