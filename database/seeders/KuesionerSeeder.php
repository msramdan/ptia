<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KuesionerSeeder extends Seeder
{
    public function run()
    {
        DB::table('kuesioner')->insert([
            ...array_map(function ($id) {
                $pertanyaanTemplates = [
                    '{params_target} termotivasi untuk terlibat secara aktif dalam setiap penugasan yang relevan dengan pelatihan ini.',
                    '{params_target} percaya diri untuk terlibat secara aktif dalam setiap kegiatan yang relevan dengan pelatihan ini.',
                    'Setelah mengikuti pelatihan, {params_target} berbagi pengetahuan yang telah diperoleh selama pelatihan kepada rekan-rekan kerja melalui kegiatan pelatihan di kantor sendiri, FGD, sharing session, atau bentuk knowledge sharing lainnya.',
                    '{params_target} mampu menerapkan ilmu yang telah diperoleh selama Pelatihan {params_kaldikDesc} pada setiap penugasan yang relevan.',
                    'Implementasi hasil pelatihan ini berdampak positif dalam meningkatkan kinerja organisasi'
                ];
                return [
                    'aspek_id' => $id,
                    'pertanyaan' => $pertanyaanTemplates[($id - 1) % 5],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, range(1, 25))
        ]);
    }
}
