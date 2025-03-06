<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapKuesionerExport implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    protected $remark;
    protected $kuesioner;
    protected $responden;
    protected $groupedLevels;

    public function __construct($remark, $kuesioner, $responden)
    {
        $this->remark = $remark;
        $this->kuesioner = $kuesioner;
        $this->responden = $responden;
        $this->groupedLevels = collect($kuesioner)->groupBy('level');
    }


    public function title(): string
    {
        return 'Rekap Kuesioner ' . $this->remark;
    }

    public function view(): View
    {
        return view('pengumpulan-data.export-rekap-kuesioner', [
            'remark' => $this->remark,
            'kuesioner' => $this->kuesioner,
            'responden' => $this->responden,
            'groupedLevels' => $this->groupedLevels,
        ]);
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:Y3'; // Sesuaikan dengan jumlah header
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }

}
