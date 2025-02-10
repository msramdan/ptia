<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BobotAspekSecondarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'bobot_aspek_sekunder' => 26.23,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('bobot_aspek_sekunder')->insert($data);
    }
}
