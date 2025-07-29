<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\InformasiUmumSheet;
use App\Exports\Sheets\DaftarRespondenSheet;
use App\Exports\Sheets\TidakTermasukRespondenSheet;
use App\Exports\Sheets\BobotSheet;
use App\Exports\Sheets\KuisionerSheet;
use App\Exports\Sheets\DataSekunderSheet;
use App\Exports\Sheets\DataInterviewSheet;
use App\Exports\Sheets\PenyebaranKuisionerSheet;
use App\Exports\Sheets\RekapKuesionerAlumniSheet;
use App\Exports\Sheets\RekapKuesionerAtasanSheet;
use App\Exports\Sheets\LaporanHasilSheet;

class HasilEvaluasiExport implements WithMultipleSheets
{
    use Exportable;

    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function sheets(): array
    {
        $sheets = [
            new InformasiUmumSheet($this->project),
            new DaftarRespondenSheet($this->project),
            new TidakTermasukRespondenSheet($this->project),
            new BobotSheet($this->project),
            new KuisionerSheet($this->project),
            new DataSekunderSheet($this->project),
            new DataInterviewSheet($this->project),
            new PenyebaranKuisionerSheet($this->project),
            new RekapKuesionerAlumniSheet($this->project),
            new RekapKuesionerAtasanSheet($this->project),
            new LaporanHasilSheet($this->project),
        ];

        return $sheets;
    }
}
