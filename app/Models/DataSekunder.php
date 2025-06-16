<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class DataSekunder extends Model
{
    use LogsActivity;
    protected $table = 'project_data_sekunder';
    protected $fillable = [
        'project_id',
        'nilai_kinerja_awal',
        'periode_awal',
        'nilai_kinerja_akhir',
        'periode_akhir',
        'satuan',
        'sumber_data',
        'unit_kerja',
        'nama_pic',
        'telpon',
        'keterangan',
        'berkas'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Data Sekunder')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $userName = Auth::user()->name ?? 'System';
        return "Data Sekunder {$this->nama_data} pada Project ID {$this->project_id} telah di-{$eventName} oleh {$userName}";
    }
}
