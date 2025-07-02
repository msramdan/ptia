<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RekapKuesionerAlumniSheet implements FromArray, WithTitle, WithEvents, ShouldAutoSize, WithColumnFormatting
{
    protected $project;
    protected $remark = 'Alumni';
    protected $groupedLevels;
    protected $headerRowCount = 3; // 3 baris untuk header

    public function __construct(Project $project)
    {
        $this->project = $project;
        $kuesioner = DB::table('project_kuesioner')
            ->selectRaw('MIN(level) as level, aspek')
            ->where('project_id', $this->project->id)
            ->where('remark', $this->remark)
            ->groupBy('aspek')
            ->orderBy('level')
            ->orderBy('aspek')
            ->get();
        $this->groupedLevels = collect($kuesioner)->groupBy('level');
    }

    public function title(): string
    {
        return 'Rekap Kuesioner Alumni';
    }

    public function array(): array
    {
        $exportData = [];
        $headerRow1 = ['No.', 'Nama Peserta', 'NIP'];
        $headerRow2 = ['', '', ''];
        $headerRow3 = ['', '', ''];

        // Membangun Header Dinamis
        foreach ($this->groupedLevels as $level => $aspeks) {
            $colspan = count($aspeks) * 5;
            $headerRow1[] = "LEVEL " . $level;
            for ($i = 1; $i < $colspan; $i++) {
                $headerRow1[] = '';
            }
            $headerRow1[] = "Total LEVEL " . $level . " (Data Primer)";

            foreach ($aspeks as $aspek) {
                $headerRow2[] = $aspek->aspek;
                for ($i = 1; $i < 5; $i++) {
                    $headerRow2[] = '';
                }
            }
            $headerRow2[] = '';

            foreach ($aspeks as $aspek) {
                $headerRow3 = array_merge($headerRow3, ['Skor', 'Konversi', 'Bobot', 'Nilai', 'Catatan']);
            }
            $headerRow3[] = '';
        }

        $exportData = [$headerRow1, $headerRow2, $headerRow3];

        // Mengambil data responden
        $responden = DB::table('project_responden')
            ->where('project_id', $this->project->id)
            ->where('status_pengisian_kuesioner_alumni', 'Sudah')
            ->get();

        // Mengambil data untuk setiap responden
        foreach ($responden as $index => $respondenItem) {
            $rowData = [$index + 1, $respondenItem->nama, $respondenItem->nip];

            // Query kompleks untuk mengambil skor, nilai, dan catatan
            $result = $this->getRespondentData($respondenItem->id);

            foreach ($this->groupedLevels as $level => $aspeks) {
                $totalNilaiLevel = 0;
                foreach ($aspeks as $aspek) {
                    $data = collect($result)->firstWhere('aspek', $aspek->aspek);
                    $nilai = $data->nilai ?? 0;
                    $totalNilaiLevel += $nilai;

                    $rowData[] = $data->rata_rata_delta ?? '-';
                    $rowData[] = $data->konversi_nilai ?? '-';
                    $rowData[] = ($data->bobot ?? '-') . '%';
                    $rowData[] = $nilai;
                    $rowData[] = $data->catatan_aspek ?? '-';
                }
                $rowData[] = $totalNilaiLevel;
            }
            $exportData[] = $rowData;
        }

        return $exportData;
    }

    protected function getRespondentData($respondenId)
    {
        // Query kompleks yang sama seperti di file blade Anda
        $sql = "WITH delta_data AS ( SELECT pjk.project_responden_id, pk.aspek_id, pk.aspek, pk.kriteria, p.id AS project_id, p.diklat_type_id, pjk.remark, COUNT(pjk.id) AS jumlah_data, SUM(pjk.nilai_sesudah - pjk.nilai_sebelum) AS total_delta, ROUND(AVG(pjk.nilai_sesudah - pjk.nilai_sebelum)) AS rata_rata_delta FROM project_jawaban_kuesioner pjk JOIN project_kuesioner pk ON pjk.project_kuesioner_id = pk.id JOIN project p ON pk.project_id = p.id WHERE pjk.project_responden_id = ? AND pjk.remark = ? GROUP BY pjk.project_responden_id, pk.aspek_id, pk.aspek, pk.kriteria, p.id, p.diklat_type_id, pjk.remark ) SELECT dd.*, (SELECT GROUP_CONCAT(pjk_inner.catatan SEPARATOR '; ') FROM project_jawaban_kuesioner pjk_inner JOIN project_kuesioner pk_inner_q ON pjk_inner.project_kuesioner_id = pk_inner_q.id WHERE pjk_inner.project_responden_id = dd.project_responden_id AND pk_inner_q.aspek_id = dd.aspek_id AND pjk_inner.remark = dd.remark AND pjk_inner.catatan IS NOT NULL AND pjk_inner.catatan != '') AS catatan_aspek, COALESCE(k.konversi, 0) AS konversi_nilai, COALESCE( CASE WHEN dd.remark = 'Alumni' THEN pba.bobot_alumni WHEN dd.remark = 'Atasan' THEN pba.bobot_atasan_langsung ELSE 0 END, 0 ) AS bobot, ROUND((COALESCE(k.konversi, 0) * COALESCE( CASE WHEN dd.remark = 'Alumni' THEN pba.bobot_alumni WHEN dd.remark = 'Atasan' THEN pba.bobot_atasan_langsung ELSE 0 END, 0 ) / 100), 2) AS nilai FROM delta_data dd LEFT JOIN konversi k ON dd.diklat_type_id = k.diklat_type_id AND dd.rata_rata_delta = k.skor AND ( (dd.kriteria = 'Skor Persepsi' AND k.jenis_skor = 'Skor Persepsi') OR (dd.kriteria = 'Delta Skor Persepsi' AND k.jenis_skor = '∆ Skor Persepsi') ) LEFT JOIN project_bobot_aspek pba ON dd.project_id = pba.project_id AND dd.aspek_id = pba.aspek_id AND dd.aspek = pba.aspek";

        return DB::select($sql, [$respondenId, $this->remark]);
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER, // Format NIP
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();

                // Header Styling & Merging
                $sheet->getStyle("A1:{$lastColumn}3")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD3D3D3']],
                ]);

                // Merge header utama
                $sheet->mergeCells('A1:A3');
                $sheet->mergeCells('B1:B3');
                $sheet->mergeCells('C1:C3');

                $startColumn = 'D';
                foreach ($this->groupedLevels as $level => $aspeks) {
                    $levelColspan = count($aspeks) * 5;
                    $endColumn = $this->getColumnIndex($startColumn, $levelColspan - 1);
                    $sheet->mergeCells("{$startColumn}1:{$endColumn}1");

                    $totalColumn = $this->getColumnIndex($endColumn, 1);
                    $sheet->mergeCells("{$totalColumn}1:{$totalColumn}3");

                    $subStartColumn = $startColumn;
                    foreach ($aspeks as $aspek) {
                        $subEndColumn = $this->getColumnIndex($subStartColumn, 4);
                        $sheet->mergeCells("{$subStartColumn}2:{$subEndColumn}2");
                        $subStartColumn = $this->getColumnIndex($subEndColumn, 1);
                    }
                    $startColumn = $this->getColumnIndex($totalColumn, 1);
                }

                // Border untuk seluruh tabel
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Text wrapping untuk kolom catatan
                $currentColIndex = 3; // Start from D
                foreach ($this->groupedLevels as $aspeks) {
                    foreach ($aspeks as $aspek) {
                        $noteCol = $this->getColumnIndexByIndex($currentColIndex + 4);
                        $sheet->getColumnDimension($noteCol)->setWidth(40);
                        $sheet->getStyle("{$noteCol}4:{$noteCol}{$lastRow}")->getAlignment()->setWrapText(true);
                        $currentColIndex += 5;
                    }
                    $currentColIndex++; // Skip total column
                }
            },
        ];
    }

    private function getColumnIndex($start, $offset)
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($start) + $offset);
    }
    private function getColumnIndexByIndex($index)
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
    }
}
