<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BobotAspekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'aspek_id' => 1,
                'bobot_alumni' => 8.94,
                'bobot_atasan_langsung' => 8.44,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aspek_id' => 2,
                'bobot_alumni' => 10.48,
                'bobot_atasan_langsung' => 9.89,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aspek_id' => 3,
                'bobot_alumni' => 7.62,
                'bobot_atasan_langsung' => 7.18,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aspek_id' => 4,
                'bobot_alumni' => 24.41,
                'bobot_atasan_langsung' => 23.04,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aspek_id' => 5,
                'bobot_alumni' => 37.95,
                'bobot_atasan_langsung' => 35.82,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data ke tabel bobot_aspek
        DB::table('bobot_aspek')->insert($data);
    }
}
