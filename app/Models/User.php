<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    // https://laravel.com/docs/11.x/sanctum#api-token-authentication
    // use \Laravel\Sanctum\HasApiTokens;

    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_nip',
        'name',
        'phone',
        'email',
        'jabatan',
        'kode_unit',
        'nama_unit',
        'avatar',

    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d H:i',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];
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
