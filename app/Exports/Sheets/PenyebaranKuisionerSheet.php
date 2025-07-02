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

class PenyebaranKuisionerSheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
{
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        // Logika Alumni (sudah benar)
        $totalAlumni = DB::table('project_responden')->where('project_id', $this->project->id)->count();
        $alumniMengisi = DB::table('project_responden')->where('project_id', $this->project->id)->where('status_pengisian_kuesioner_alumni', 'Sudah')->count();
        $alumniBelumMengisi = $totalAlumni - $alumniMengisi;
        $persenAlumni = $totalAlumni > 0 ? round(($alumniMengisi / $totalAlumni) * 100, 2) . '%' : '0%';

        // Logika Atasan (Perbaikan)
        $totalAtasan = DB::table('project_responden')->where('project_id', $this->project->id)->whereNotNull('nama_atasan')->where('nama_atasan', '!=', '')->count();
        $atasanMengisi = DB::table('project_responden')->where('project_id', $this->project->id)->where('status_pengisian_kuesioner_atasan', 'Sudah')->count();
        $atasanBelumMengisi = $totalAtasan - $atasanMengisi;
        $persenAtasan = $totalAtasan > 0 ? round(($atasanMengisi / $totalAtasan) * 100, 2) . '%' : '0%';

        return collect([
            ['Jumlah Responden Alumni', $totalAlumni ?? 0],
            ['Jumlah Responden Atasan', $totalAtasan ?? 0],
            ['Alumni Sudah Mengisi', $alumniMengisi ?? 0],
            ['Atasan Sudah Mengisi', $atasanMengisi ?? 0],
            ['Alumni Belum Mengisi', $alumniBelumMengisi ?? 0],
            ['Atasan Belum Mengisi', $atasanBelumMengisi ?? 0],
            ['Persentase Keterisian Alumni', $persenAlumni],
            ['Persentase Keterisian Atasan', $persenAtasan],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        // Style hanya diterapkan pada kolom A (header)
        $sheet->getStyle('A1:A' . $lastRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F81BD']],
        ]);
        // Border untuk semua data
        $sheet->getStyle('A1:B' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
        // Rata kiri untuk kolom nilai
        $sheet->getStyle('B1:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [];
    }

    public function title(): string
    {
        return 'Penyebaran Kuisioner';
    }
}
