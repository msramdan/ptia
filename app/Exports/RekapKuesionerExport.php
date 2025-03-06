<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;

class RekapKuesionerExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $project;
    protected $remark;
    protected $kuesioner;
    protected $responden;
    protected $groupedLevels;

    public function __construct($project, $remark, $kuesioner, $responden)
    {
        $this->project = $project;
        $this->remark = $remark;
        $this->kuesioner = $kuesioner;
        $this->responden = $responden;

        // Kelompokkan kuesioner berdasarkan level
        $this->groupedLevels = collect($kuesioner)->groupBy('level');
    }

    public function collection()
    {
        $rows = [];

        foreach ($this->responden as $index => $respondenItem) {
            $sql = "WITH delta_data AS (
                SELECT
                    project_jawaban_kuesioner.project_responden_id,
                    project_kuesioner.aspek_id,
                    project_kuesioner.aspek,
                    project_kuesioner.kriteria,
                    project.id AS project_id,
                    project.diklat_type_id,
                    project_jawaban_kuesioner.remark,
                    COUNT(project_jawaban_kuesioner.id) AS jumlah_data,
                    SUM(project_jawaban_kuesioner.nilai_sesudah - project_jawaban_kuesioner.nilai_sebelum) AS total_delta,
                    ROUND(AVG(project_jawaban_kuesioner.nilai_sesudah - project_jawaban_kuesioner.nilai_sebelum)) AS rata_rata_delta
                FROM project_jawaban_kuesioner
                JOIN project_kuesioner ON project_jawaban_kuesioner.project_kuesioner_id = project_kuesioner.id
                JOIN project ON project_kuesioner.project_id = project.id
                WHERE project_jawaban_kuesioner.project_responden_id = :responden_id
                    AND project_jawaban_kuesioner.remark = :remark
                GROUP BY
                    project_jawaban_kuesioner.project_responden_id,
                    project_kuesioner.aspek_id,
                    project_kuesioner.aspek,
                    project_kuesioner.kriteria,
                    project.diklat_type_id,
                    project_jawaban_kuesioner.remark
            )
            SELECT
                delta_data.*,
                COALESCE(konversi.konversi, 0) AS konversi_nilai,
                COALESCE(
                    CASE
                        WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                        WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                        ELSE 0
                    END, 0
                ) AS bobot,
                -- Perhitungan nilai
                ROUND((COALESCE(konversi.konversi, 0) *
                       COALESCE(
                           CASE
                               WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                               WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                               ELSE 0
                           END, 0
                       ) / 100), 2) AS nilai
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
                AND delta_data.aspek_id = project_bobot_aspek.aspek_id
                AND delta_data.aspek = project_bobot_aspek.aspek;";

            $result = DB::select($sql, [
                'responden_id' => $respondenItem->id,
                'remark' => $this->remark,
            ]);

            $row = [
                $index + 1, // No.
                $respondenItem->nama, // Nama
                $respondenItem->nip, // NIP
            ];

            $total_level_3 = 0;
            $total_level_4 = 0;

            foreach ($this->groupedLevels as $level => $aspeks) {
                foreach ($aspeks as $aspek) {
                    $queryResult = collect($result)->firstWhere('aspek', $aspek->aspek);

                    $row[] = $queryResult ? $queryResult->rata_rata_delta : '-'; // Skor
                    $row[] = $queryResult ? $queryResult->konversi_nilai : '-'; // Konversi
                    $row[] = $queryResult ? $queryResult->bobot : '-'; // Bobot
                    $row[] = $queryResult ? $queryResult->nilai : 0; // Nilai

                    if ($level == 3) {
                        $total_level_3 += $queryResult ? $queryResult->nilai : 0;
                    } elseif ($level == 4) {
                        $total_level_4 += $queryResult ? $queryResult->nilai : 0;
                    }
                }

                $row[] = $level == 3 ? $total_level_3 : $total_level_4; // Total LEVEL
            }

            $row[] = ''; // Kolom Aksi (kosong karena tidak diekspor ke Excel)
            $rows[] = $row;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $headings = [];

        // Baris 1: No., Nama, NIP, LEVEL, Total LEVEL, Aksi
        $row1 = ['No.', 'Nama', 'NIP'];

        foreach ($this->groupedLevels as $level => $aspeks) {
            $row1[] = 'LEVEL ' . $level; // LEVEL 3, LEVEL 4, dst.
            $row1[] = 'Total <br>LEVEL ' . $level . '<br> (Data Primer)'; // Total LEVEL 3, Total LEVEL 4, dst.
        }

        $row1[] = 'Aksi'; // Kolom Aksi
        $headings[] = $row1;

        // Baris 2: Aspek (colspan untuk setiap aspek)
        $row2 = ['', '', '']; // No., Nama, NIP kosong

        foreach ($this->groupedLevels as $level => $aspeks) {
            foreach ($aspeks as $aspek) {
                $row2[] = $aspek->aspek; // Nama Aspek
                $row2[] = ''; // Kolom kosong untuk Total LEVEL
            }
        }

        $row2[] = ''; // Kolom Aksi kosong
        $headings[] = $row2;

        // Baris 3: Skor, Konversi, Bobot, Nilai
        $row3 = ['', '', '']; // No., Nama, NIP kosong

        foreach ($this->groupedLevels as $level => $aspeks) {
            foreach ($aspeks as $aspek) {
                $row3[] = 'Skor';
                $row3[] = 'Konversi';
                $row3[] = 'Bobot';
                $row3[] = 'Nilai';
                $row3[] = ''; // Kolom kosong untuk Total LEVEL
            }
        }

        $row3[] = ''; // Kolom Aksi kosong
        $headings[] = $row3;

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        // Merge header untuk No., Nama, NIP
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', 'Data Rekap Kuesioner');

        // Merge header untuk LEVEL dan Total LEVEL
        $colIndex = 4; // Mulai dari kolom D
        foreach ($this->groupedLevels as $level => $aspeks) {
            $startCol = $colIndex;
            $endCol = $colIndex + (count($aspeks) * 4 - 1);

            // Merge untuk LEVEL
            $startCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol) . '1';
            $endCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol) . '1';
            $sheet->mergeCells("$startCell:$endCell");

            // Merge untuk Total LEVEL
            $totalCell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol + 1) . '1';
            $sheet->mergeCells("$totalCell:$totalCell");

            $colIndex = $endCol + 2; // Geser ke LEVEL berikutnya
        }

        // Style untuk header
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '3')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
        ]);

        // Style untuk tabel
        $sheet->getStyle('A2:' . $sheet->getHighestColumn() . (count($this->responden) + 3))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Auto-size kolom
                $highestColumn = $event->sheet->getHighestColumn();
                $currentColumn = 'A';

                // Loop untuk mengiterasi semua kolom dari A hingga kolom tertinggi
                while ($currentColumn !== $highestColumn) {
                    $event->sheet->getColumnDimension($currentColumn)->setAutoSize(true);
                    $currentColumn++;
                }

                // Pastikan kolom tertinggi juga diatur
                $event->sheet->getColumnDimension($highestColumn)->setAutoSize(true);
            },
        ];
    }
}
