<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('setting')->insert([
            'nama_aplikasi' => 'Aplikasi PTIA',
            'tentang_aplikasi' => 'Deskripsi singkat tentang aplikasi ini.',
            'logo' => null,
            'logo_login' => null,
            'favicon' => null,
            'pengumuman' => 'Pengumuman default aplikasi.',
            'is_aktif_pengumuman' => 'No',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
