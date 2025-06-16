<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends SpatieActivity
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Event ini akan berjalan setiap kali sebuah log akan dibuat
        static::creating(function (Activity $activity) {
            $request = request();

            // Tambahkan IP Address dan User Agent ke dalam kolom 'properties'
            $activity->properties = $activity->properties->merge([
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        });
    }

    /**
     * Override relasi bawaan untuk memastikan tipe return-nya benar.
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Override relasi bawaan untuk memastikan tipe return-nya benar.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
