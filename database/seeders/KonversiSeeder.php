<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class KonversiSeeder extends Seeder
{
    public function run()
    {
        $data = [];
        foreach (range(1, 5) as $diklatTypeId) {
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => 'Skor Persepsi', 'skor' => 1, 'konversi' => 0.00, 'created_at' => now(), 'updated_at' => now()];
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => 'Skor Persepsi', 'skor' => 2, 'konversi' => 63.00, 'created_at' => now(), 'updated_at' => now()];
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => 'Skor Persepsi', 'skor' => 3, 'konversi' => 83.00, 'created_at' => now(), 'updated_at' => now()];
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => 'Skor Persepsi', 'skor' => 4, 'konversi' => 100.00, 'created_at' => now(), 'updated_at' => now()];
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => '∆ Skor Persepsi', 'skor' => 0, 'konversi' => 0.00, 'created_at' => now(), 'updated_at' => now()];
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => '∆ Skor Persepsi', 'skor' => 1, 'konversi' => 63.00, 'created_at' => now(), 'updated_at' => now()];
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => '∆ Skor Persepsi', 'skor' => 2, 'konversi' => 88.00, 'created_at' => now(), 'updated_at' => now()];
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => '∆ Skor Persepsi', 'skor' => 3, 'konversi' => 100.00, 'created_at' => now(), 'updated_at' => now()];
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => 'Skor Data Sekunder', 'skor' => 0, 'konversi' => 0.00, 'created_at' => now(), 'updated_at' => now()];
            $data[] = ['diklat_type_id' => $diklatTypeId, 'jenis_skor' => 'Skor Data Sekunder', 'skor' => 1, 'konversi' => 100.00, 'created_at' => now(), 'updated_at' => now()];
        }
        DB::table('konversi')->insert($data);
    }
}
