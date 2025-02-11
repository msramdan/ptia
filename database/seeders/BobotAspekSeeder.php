<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BobotAspekSeeder extends Seeder
{

    public function run(): void
    {
        $bobotData = [
            [8.94, 8.44],
            [10.48, 9.89],
            [7.62, 7.18],
            [24.41, 23.04],
            [37.95, 35.82],
        ];

        $data = [];
        $timestamp = now();

        for ($i = 1; $i <= 25; $i++) {
            $index = ($i - 1) % count($bobotData);
            $data[] = [
                'aspek_id' => $i,
                'bobot_alumni' => $bobotData[$index][0],
                'bobot_atasan_langsung' => $bobotData[$index][1],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        DB::table('bobot_aspek')->insert($data);
    }
}
