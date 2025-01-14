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
        $data = [
            [
                'level' => '3',
                'aspek' => 'Motivasi',
                'urutan' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => '3',
                'aspek' => 'Kepercayaan Diri',
                'urutan' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => '3',
                'aspek' => 'Kemampuan Membagikan Keilmuan',
                'urutan' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => '3',
                'aspek' => 'Kemampuan Implementasi Keilmuan',
                'urutan' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level' => '4',
                'aspek' => 'Hasil Pelatihan',
                'urutan' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('aspek')->insert($data);
    }
}
