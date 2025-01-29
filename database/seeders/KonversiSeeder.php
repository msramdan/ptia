<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class KonversiSeeder extends Seeder
{
    public function run()
    {
        DB::table('konversi')->insert([
            ['jenis_skor' => 'Skor Persepsi', 'skor' => 1, 'konversi' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['jenis_skor' => 'Skor Persepsi', 'skor' => 2, 'konversi' => 63.00, 'created_at' => now(), 'updated_at' => now()],
            ['jenis_skor' => 'Skor Persepsi', 'skor' => 3, 'konversi' => 83.00, 'created_at' => now(), 'updated_at' => now()],
            ['jenis_skor' => 'Skor Persepsi', 'skor' => 4, 'konversi' => 100.00, 'created_at' => now(), 'updated_at' => now()],
            ['jenis_skor' => '∆ Skor Persepsi', 'skor' => 0, 'konversi' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['jenis_skor' => '∆ Skor Persepsi', 'skor' => 1, 'konversi' => 63.00, 'created_at' => now(), 'updated_at' => now()],
            ['jenis_skor' => '∆ Skor Persepsi', 'skor' => 2, 'konversi' => 88.00, 'created_at' => now(), 'updated_at' => now()],
            ['jenis_skor' => '∆ Skor Persepsi', 'skor' => 3, 'konversi' => 100.00, 'created_at' => now(), 'updated_at' => now()],
            ['jenis_skor' => 'Skor Data Sekunder', 'skor' => 0, 'konversi' => 0.00, 'created_at' => now(), 'updated_at' => now()],
            ['jenis_skor' => 'Skor Data Sekunder', 'skor' => 1, 'konversi' => 100.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
