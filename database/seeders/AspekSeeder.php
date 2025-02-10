<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AspekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Loop untuk id diklat_type_id dari 1 hingga 5
        foreach (range(1, 5) as $diklatTypeId) {
            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'level' => '3',
                'aspek' => 'Motivasi',
                'kriteria' => 'Delta Skor Persepsi',
                'urutan' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'level' => '3',
                'aspek' => 'Kepercayaan Diri',
                'kriteria' => 'Delta Skor Persepsi',
                'urutan' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'level' => '3',
                'aspek' => 'Kemampuan Membagikan Keilmuan',
                'kriteria' => 'Skor Persepsi',
                'urutan' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'level' => '3',
                'aspek' => 'Kemampuan Implementasi Keilmuan',
                'kriteria' => 'Delta Skor Persepsi',
                'urutan' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'level' => '4',
                'aspek' => 'Hasil Pelatihan',
                'kriteria' => 'Delta Skor Persepsi',
                'urutan' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert data ke tabel aspek
        DB::table('aspek')->insert($data);
    }
}
