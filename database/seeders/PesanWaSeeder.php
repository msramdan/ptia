<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PesanWaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pesan_wa')->insert([
            'text_pesan_alumni' => "<p>*Yth. Bapak/Ibu Alumni {params_nama_diklat}*</p><p>Dalam rangka evaluasi pasca pembelajaran atas *{params_nama_diklat}*, kami harapkan partisipasi Bapak/Ibu sebagai Alumni pelatihan, Mohon kesediaannya untuk mengisi kuesioner melalui tautan berikut :</p><p>*{params_link}*</p><p>Paling lambat tanggal *{params_deadline}*.</p><p>Pusdiklatwas BPKP menjamin kerahasiaan informasi yang diberikan.</p><p>Narahubung berkenaan evaluasi ini dapat menghubungi nomor WA *{params_wa_pic}* a.n. Sdr. *{params_pic}* Terima kasih.&nbsp;</p><p>*Tim Evaluasi Pusdiklatwas BPKP*&nbsp;</p><p>&nbsp;</p><p>Catatan: Jika link tidak bisa diklik, mohon terlebih dahulu menyimpan nomor GIA Corpu ini ke dalam daftar kontak Bapak/Ibu.</p>",
            'text_pesan_atasan' => "<p>*Yth. Bapak/Ibu Atasan Langsung Alumni*</p><p>Dalam rangka evaluasi pasca pembelajaran atas *{params_nama_diklat}*, kami harapkan partisipasi Bapak/Ibu sebagai Atasan Langsung dari *Sdr. {nama_peserta}*, Mohon kesediaannya untuk mengisi kuesioner melalui tautan berikut :</p><p>*{params_link}*</p><p>Paling lambat tanggal *{params_deadline}*.</p><p>Pusdiklatwas BPKP menjamin kerahasiaan informasi yang diberikan.</p><p>Narahubung berkenaan evaluasi ini dapat menghubungi nomor WA *{params_wa_pic}* a.n. Sdr. *{params_pic}* Terima kasih.&nbsp;</p><p>*Tim Evaluasi Pusdiklatwas BPKP*&nbsp;</p><p>&nbsp;</p><p>Catatan: Jika link tidak bisa diklik, mohon terlebih dahulu menyimpan nomor GIA Corpu ini ke dalam daftar kontak Bapak/Ibu.</p>",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
