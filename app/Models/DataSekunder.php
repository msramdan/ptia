<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSekunder extends Model
{
    protected $fillable = [
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
}
