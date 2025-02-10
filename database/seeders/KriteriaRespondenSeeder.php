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
            'nilai_post_test_minimal' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
