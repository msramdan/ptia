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

class HasilEvaluasiExport implements WithMultipleSheets
{
    use Exportable;

    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [
            new InformasiUmumSheet($this->project),
            new DaftarRespondenSheet($this->project),
            new TidakTermasukRespondenSheet($this->project),
            new BobotSheet($this->project),
            new KuisionerSheet($this->project),
        ];

        return $sheets;
    }
}
