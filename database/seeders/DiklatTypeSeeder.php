<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiklatTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('diklat_type')->insert([
            ['nama_diklat_type' => 'Fungsional Auditor'],
            ['nama_diklat_type' => 'TS SPIP & TS APIP'],
            ['nama_diklat_type' => 'Kedinasan & Manajerial'],
            ['nama_diklat_type' => 'Sertifikasi Non JFA'],
            ['nama_diklat_type' => 'MOOC & Micro Learning'],
        ]);
    }
}
