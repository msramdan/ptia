<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'nama_aplikasi',
        'tentang_aplikasi',
        'logo',
        'logo_login',
        'favicon',
        'pengumuman',
        'is_aktif_pengumuman',
        'jam_mulai',
        'jam_selesai',
        'hari_libur',
    ];

    protected $casts = [
        'hari_libur' => 'array', // Cast kolom JSON ke array
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['nama_aplikasi' => 'string', 'tentang_aplikasi' => 'string', 'logo' => 'string', 'logo_login' => 'string', 'favicon' => 'string', 'pengumuman' => 'string', 'created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s'];
    }
}
