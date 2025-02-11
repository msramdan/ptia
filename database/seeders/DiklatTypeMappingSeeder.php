<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiklatTypeMappingSeeder extends Seeder
{
    public function run()
    {
        DB::table('diklat_type_mapping')->insert([
            [
                'diklat_type_id' => 1,
                'diklatTypeName' => 'Fungsional Auditor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'diklat_type_id' => 2,
                'diklatTypeName' => 'TS SPIP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'diklat_type_id' => 2,
                'diklatTypeName' => 'TS APIP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'diklat_type_id' => 3,
                'diklatTypeName' => 'Kedinasan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'diklat_type_id' => 3,
                'diklatTypeName' => 'Manajerial',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'diklat_type_id' => 4,
                'diklatTypeName' => 'Sertifikasi Non JFA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'diklat_type_id' => 5,
                'diklatTypeName' => 'MOOC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'diklat_type_id' => 5,
                'diklatTypeName' => 'Micro Learning',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
