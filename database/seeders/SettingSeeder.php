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
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '17:00:00',
            'hari_jalan_cron' => json_encode(["1", "2", "3", "4", "5"]),
            'deadline_pengisian' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
