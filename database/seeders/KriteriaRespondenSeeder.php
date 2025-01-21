<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaRespondenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kriteria_responden')->insert([
            'nilai_post_test' => json_encode(['Naik', 'Tetap']),
            'nilai_pre_test_minimal' => 50,
            'nilai_post_test_minimal' => 50,
            'nilai_kenaikan_pre_post' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
