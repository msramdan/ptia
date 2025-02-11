<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiklatTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('diklat_type')->insert([
            [
                'nama_diklat_type' => 'Fungsional Auditor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diklat_type' => 'TS SPIP & TS APIP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diklat_type' => 'Kedinasan & Manajerial',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diklat_type' => 'Sertifikasi Non JFA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_diklat_type' => 'MOOC & Micro Learning',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
