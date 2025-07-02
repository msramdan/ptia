<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory, LogsActivity;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['user_id', 'kode_project', 'kaldikID', 'kaldikDesc'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['kode_project' => 'string', 'kaldikDesc' => 'string', 'created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s'];
    }

    /**
     * Mendapatkan user (evaluator) yang memiliki project.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
