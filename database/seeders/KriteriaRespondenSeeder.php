<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaRespondenSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];

        foreach (range(1, 5) as $diklatTypeId) {
            $data[] = [
                'diklat_type_id' => $diklatTypeId,
                'nilai_post_test' => json_encode(['Turun']),
                'nilai_post_test_minimal' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('kriteria_responden')->insert($data);
    }

}
