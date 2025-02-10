<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class IndikatorDampakSeeder extends Seeder
{
    public function run()
    {
        foreach (range(1, 5) as $diklatTypeId) {
            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'nilai_minimal' => 0,
                'nilai_maksimal' => 25,
                'kriteria_dampak' => 'Tidak berdampak',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'nilai_minimal' => 26,
                'nilai_maksimal' => 50,
                'kriteria_dampak' => 'Kurang berdampak',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'nilai_minimal' => 51,
                'nilai_maksimal' => 75,
                'kriteria_dampak' => 'Cukup berdampak',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'nilai_minimal' => 76,
                'nilai_maksimal' => 100,
                'kriteria_dampak' => 'Sangat berdampak',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('indikator_dampak')->insert($data);
    }
}
