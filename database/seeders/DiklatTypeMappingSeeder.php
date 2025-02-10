<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiklatTypeMappingSeeder extends Seeder
{
    public function run()
    {
        DB::table('diklat_type_mapping')->insert([
            ['diklat_type_id' => 1, 'diklatTypeName' => 'Fungsional Auditor'],
            ['diklat_type_id' => 2, 'diklatTypeName' => 'TS SPIP'],
            ['diklat_type_id' => 2, 'diklatTypeName' => 'TS APIP'],
            ['diklat_type_id' => 3, 'diklatTypeName' => 'Kedinasan'],
            ['diklat_type_id' => 3, 'diklatTypeName' => 'Manajerial'],
            ['diklat_type_id' => 4, 'diklatTypeName' => 'Sertifikasi Non JFA'],
            ['diklat_type_id' => 5, 'diklatTypeName' => 'MOOC'],
            ['diklat_type_id' => 5, 'diklatTypeName' => 'Micro Learning'],
        ]);
    }
}
