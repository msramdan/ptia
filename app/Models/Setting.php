<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;


class Setting extends Model
{
    use HasFactory, LogsActivity;

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
        'hari_jalan_cron',
        'deadline_pengisian',
        'cron_notif_alumni',
        'cron_notif_atasan',
        'cron_auto_insert_expired_atasan',
        'cron_auto_create_project',
    ];

    protected $casts = [
        'hari_jalan_cron' => 'array',
        'deadline_pengisian' => 'integer',
        'cron_notif_alumni' => 'string',
        'cron_notif_atasan' => 'string',
        'cron_auto_insert_expired_atasan' => 'string',
        'cron_auto_create_project' => 'string',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nama_aplikasi' => 'string',
            'tentang_aplikasi' => 'string',
            'logo' => 'string',
            'logo_login' => 'string',
            'favicon' => 'string',
            'pengumuman' => 'string',
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deadline_pengisian' => 'integer',
            'hari_jalan_cron' => 'array',
            'cron_notif_alumni' => 'string',
            'cron_notif_atasan' => 'string',
            'cron_auto_insert_expired_atasan' => 'string',
            'cron_auto_create_project' => 'string',
        ];
    }

    // Tambahkan method ini untuk konfigurasi log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Setting') // Nama log
            ->logFillable() // Catat semua atribut di $fillable
            ->logOnlyDirty() // Hanya catat jika ada perubahan
            ->dontSubmitEmptyLogs(); // Jangan buat log jika tidak ada perubahan
    }

    // Tambahkan method ini untuk deskripsi custom
    public function getDescriptionForEvent(string $eventName): string
    {
        $userName = Auth::user()->name ?? 'System';
        return "Pengaturan telah di-{$eventName} oleh {$userName}";
    }
}
