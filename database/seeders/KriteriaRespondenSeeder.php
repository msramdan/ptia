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
            'diklat_type_id' => 1,
            'nilai_post_test' => json_encode(['Turun']),
            'nilai_post_test_minimal' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
