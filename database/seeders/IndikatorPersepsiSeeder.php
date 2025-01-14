<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndikatorPersepsiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua data aspek
        $aspeks = DB::table('aspek')->get();

        // Definisikan indikator_persepsi dan kriteria_persepsi
        $indikatorPersepsi = [
            ['indikator_persepsi' => '1', 'kriteria_persepsi' => 'Sangat tidak setuju'],
            ['indikator_persepsi' => '2', 'kriteria_persepsi' => 'Tidak setuju'],
            ['indikator_persepsi' => '3', 'kriteria_persepsi' => 'Setuju'],
            ['indikator_persepsi' => '4', 'kriteria_persepsi' => 'Sangat setuju'],
        ];

        $data = [];

        // Loop setiap aspek dan buat entri untuk indikator persepsi
        foreach ($aspeks as $aspek) {
            foreach ($indikatorPersepsi as $indikator) {
                $data[] = [
                    'aspek_id' => $aspek->id,
                    'indikator_persepsi' => $indikator['indikator_persepsi'],
                    'kriteria_persepsi' => $indikator['kriteria_persepsi'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Masukkan data ke tabel indikator_persepsi
        DB::table('indikator_persepsi')->insert($data);
    }
}
