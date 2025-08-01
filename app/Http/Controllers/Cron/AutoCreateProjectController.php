<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class AutoCreateProjectController extends Controller
{

    public function autoCreate()
    {
        $setting = Setting::first();

        if (!$setting) {
            Log::error("Pengaturan CRON tidak ditemukan di database.");
            return response()->json(['message' => 'Pengaturan CRON tidak ditemukan.'], 404);
        }
        if ($setting->cron_auto_insert_expired_atasan !== 'Yes') {
            sendNotifTelegram("❌ Cron auto create project dinonaktifkan di pengaturan.", 'Cron');
            return response()->json(['message' => 'Cron auto create project dinonaktifkan di pengaturan.'], 200);
        }

        $startTime = now();
        sendNotifTelegram("🚀 Cron Job Auto Create Project Dimulai\n📅 Waktu Mulai: {$startTime}", 'Cron');

        $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-kaldik";
        $apiToken = config('services.pusdiklatwas.api_token');
        $readyGenerate = DB::table('project')->pluck('kaldikID')->toArray();

        // Query parameters
        $queryParams = [
            'api_key' => $apiToken,
            'is_cron_auto_create' => 'Yes',
        ];

        // Kirim request GET dengan query params di URL dan body JSON
        $response = Http::retry(3, 100)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->withBody(json_encode(['ready_generate' => $readyGenerate]), 'application/json')
            ->get($apiUrl . '?' . http_build_query($queryParams));
        $data = $response->json()['data'] ?? [];

        if (empty($data)) {
            $endTime = now();
            sendNotifTelegram("⚠️ Cron Job Auto Create Project Selesai\nTidak ada project dari API info diklat lebih dari 3 bulan.\n📅 Waktu Selesai: {$endTime}", 'Cron');
            return response()->json(['status' => false, 'message' => "Tidak ada data dari API."], 200);
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($data as $row) {
            DB::beginTransaction();

            try {
                if (DB::table('project')->where('kaldikID', $row['kaldikID'])->exists()) {
                    sendNotifTelegram("⏳ Project sudah ada untuk KaldikID: {$row['kaldikID']}", 'Cron');
                    DB::commit();
                    continue;
                }

                $kode_project = Str::upper(Str::random(8));

                $diklatType = DB::table('diklat_type_mapping')
                    ->where('diklatTypeName', $row['diklatTypeName'])
                    ->first();

                if (!$diklatType) {
                    throw new \Exception("❌ Type diklat tidak ditemukan: {$row['diklatTypeName']}");
                }

                $projectId = DB::table('project')->insertGetId([
                    'diklat_type_id' => $diklatType->diklat_type_id,
                    'kode_project'   => $kode_project,
                    'kaldikID'       => $row['kaldikID'],
                    'diklatTypeName' => $row['diklatTypeName'],
                    'kaldikDesc'     => $row['kaldikDesc'],
                    'user_id'        => null,
                    'tanggal_mulai'     => $row['startDate'],
                    'tanggal_selesai'   => $row['endDate'],
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // Insert project_kriteria_responden
                $kriteriaResponden = DB::table('kriteria_responden')
                    ->where('diklat_type_id', $diklatType->diklat_type_id)
                    ->first();

                if (!$kriteriaResponden) {
                    throw new \Exception("❌ Tidak ada data kriteria responden.");
                }



                // Ambil data responden dari API
                $statusValues = json_decode($kriteriaResponden->nilai_post_test, true);
                $statusQuery = implode('&', array_map(fn($status) => 'status=' . urlencode($status), $statusValues));
                $respondenUrl = config('services.pusdiklatwas.endpoint') . "/len-peserta-diklat/{$row['kaldikID']}?" . http_build_query([
                    'api_key'             => $apiToken,
                    'is_pagination'       => 'No',
                    'post_test_minimal'   => $kriteriaResponden->nilai_post_test_minimal,
                ]) . "&" . $statusQuery;

                $response = Http::retry(3, 100)->get($respondenUrl);

                if ($response->failed()) {
                    throw new \Exception("⚠️ Gagal mengambil data responden dari API.");
                }

                $respondenData = $response->json();

                // Validasi struktur data
                if (
                    !isset($respondenData['data_include'], $respondenData['data_exclude']) ||
                    !is_array($respondenData['data_include']) ||
                    !is_array($respondenData['data_exclude'])
                ) {
                    throw new \Exception("⚠️ Format data responden dari API tidak valid.");
                }

                // Simpan ke project_kriteria_responden
                DB::table('project_kriteria_responden')->insert([
                    'project_id'                    => $projectId,
                    'nilai_post_test'              => $kriteriaResponden->nilai_post_test,
                    'nilai_post_test_minimal'      => $kriteriaResponden->nilai_post_test_minimal,
                    'total_peserta'                => $respondenData['total'] ?? 0,
                    'total_termasuk_responden'     => $respondenData['total_include'] ?? 0,
                    'total_tidak_termasuk_responden' => $respondenData['total_exclude'] ?? 0,
                    'created_at'                   => now(),
                    'updated_at'                   => now(),
                ]);

                // Data responden termasuk (INCLUDE)
                $includeData = collect($respondenData['data_include'])->map(function ($responden) use ($projectId) {
                    return [
                        'project_id'       => $projectId,
                        'peserta_id'       => $responden['pesertaID'],
                        'nama'             => $responden['pesertaNama'],
                        'nip'              => $responden['pesertaNIP'],
                        'telepon'          => $responden['pesertaTelepon'],
                        'jabatan'          => trim($responden['jabatanFullName']),
                        'unit'             => $responden['unitName'],
                        'nilai_pre_test'   => $responden['pesertaNilaiPreTest'],
                        'nilai_post_test'  => $responden['pesertaNilaiPostTest'],
                        'token'            => Str::random(12),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                })->toArray();

                if (!empty($includeData)) {
                    DB::table('project_responden')->insert($includeData);
                }

                // Data responden TIDAK termasuk (EXCLUDE)
                $excludeData = collect($respondenData['data_exclude'])->map(function ($responden) use ($projectId) {
                    return [
                        'project_id'       => $projectId,
                        'peserta_id'       => $responden['pesertaID'],
                        'nama'             => $responden['pesertaNama'],
                        'nip'              => $responden['pesertaNIP'],
                        'telepon'          => $responden['pesertaTelepon'] ?? null,
                        'jabatan'          => trim($responden['jabatanFullName']),
                        'unit'             => $responden['unitName'],
                        'nilai_pre_test'   => $responden['pesertaNilaiPreTest'] ?? null,
                        'nilai_post_test'  => $responden['pesertaNilaiPostTest'] ?? null,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                })->toArray();

                if (!empty($excludeData)) {
                    DB::table('project_responden_exclude')->insert($excludeData);
                }


                // Insert ke project_pesan_wa
                $pesanWa = DB::table('pesan_wa')->first();
                if (!$pesanWa) {
                    throw new \Exception("⚠️ Config pesan WA tidak ditemukan.");
                }

                DB::table('project_pesan_wa')->insert([
                    'project_id'        => $projectId,
                    'text_pesan_alumni' => str_replace(
                        ['{params_nama_diklat}'],
                        [$row['kaldikDesc']],
                        $pesanWa->text_pesan_alumni
                    ),
                    'text_pesan_atasan' => str_replace(
                        ['{params_nama_diklat}'],
                        [$row['kaldikDesc']],
                        $pesanWa->text_pesan_atasan
                    ),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Insert data ke table project_bobot_aspek
                $dataBobot = DB::table('bobot_aspek')
                    ->join('aspek', 'bobot_aspek.aspek_id', '=', 'aspek.id')
                    ->select('bobot_aspek.aspek_id', 'bobot_aspek.bobot_alumni', 'bobot_aspek.bobot_atasan_langsung', 'aspek.level', 'aspek.aspek')
                    ->where('aspek.diklat_type_id', $diklatType->diklat_type_id)
                    ->get();

                if ($dataBobot->isEmpty()) {
                    throw new \Exception("⚠️ No bobot aspek found.");
                }

                $insertData = $dataBobot->map(function ($item) use ($projectId) {
                    return [
                        'project_id' => $projectId,
                        'aspek_id' => $item->aspek_id,
                        'level' => $item->level,
                        'aspek' => $item->aspek,
                        'bobot_alumni' => $item->bobot_alumni,
                        'bobot_atasan_langsung' => $item->bobot_atasan_langsung,
                    ];
                })->toArray();
                DB::table('project_bobot_aspek')->insert($insertData);

                // 6.insert data ke table project_bobot_aspek_sekunder
                $dataBobotSekunder = DB::table('bobot_aspek_sekunder')
                    ->where('diklat_type_id', $diklatType->diklat_type_id)
                    ->first();

                if (!$dataBobotSekunder) {
                    throw new \Exception("⚠️ No bobot aspek sekunder found.");
                }

                DB::table('project_bobot_aspek_sekunder')->insert([
                    'project_id' => $projectId,
                    'bobot_aspek_sekunder' => $dataBobotSekunder->bobot_aspek_sekunder,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 7. Insert data ke table project_kuesioner
                $kaldikDesc = $row['kaldikDesc'] ?? 'Pelatihan Default';
                $pertanyaanList = DB::table('kuesioner')
                    ->join('aspek', 'kuesioner.aspek_id', '=', 'aspek.id')
                    ->where('aspek.diklat_type_id', $diklatType->diklat_type_id)
                    ->select('aspek.id as aspek_id', 'aspek.level', 'aspek.aspek', 'aspek.kriteria', 'kuesioner.pertanyaan')
                    ->get();

                $kuesionerData = [];
                foreach ($pertanyaanList as $pertanyaanItem) {
                    $pertanyaanAlumni = str_replace(
                        ["{params_target}", "{params_nama_diklat}"],
                        ["Saya", $kaldikDesc],
                        $pertanyaanItem->pertanyaan
                    );

                    $kuesionerData[] = [
                        'project_id'  => $projectId,
                        'aspek_id'    => $pertanyaanItem->aspek_id,
                        'level'       => $pertanyaanItem->level,
                        'aspek'       => $pertanyaanItem->aspek,
                        'kriteria'    => $pertanyaanItem->kriteria,
                        'remark'      => 'Alumni',
                        'pertanyaan'  => $pertanyaanAlumni,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];

                    $pertanyaanAtasan = str_replace(
                        ["{params_target}", "{params_nama_diklat}"],
                        ["Alumni", $kaldikDesc],
                        $pertanyaanItem->pertanyaan
                    );

                    $kuesionerData[] = [
                        'project_id'  => $projectId,
                        'aspek_id'    => $pertanyaanItem->aspek_id,
                        'level'       => $pertanyaanItem->level,
                        'aspek'       => $pertanyaanItem->aspek,
                        'kriteria'    => $pertanyaanItem->kriteria,
                        'remark'      => 'Atasan',
                        'pertanyaan'  => $pertanyaanAtasan,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }

                DB::table('project_kuesioner')->insert($kuesionerData);
                DB::commit();
                $successCount++;
                sendNotifTelegram("✅ Project berhasil dibuat\nID: {$row['kaldikID']}\nNama Diklat: {$row['kaldikDesc']}", 'Cron');
            } catch (\Exception $e) {
                DB::rollBack();
                $failCount++;
                sendNotifTelegram("❌ Error: " . $e->getMessage(), 'Cron');
                continue;
            }
        }

        $endTime = now();
        sendNotifTelegram("✅ Cron Job Selesai\nBerhasil Create {$successCount}, Gagal Create {$failCount}\n📅 Waktu Selesai: {$endTime}", 'Cron');
        return response()->json(['status' => true, 'message' => "Proses selesai."]);
    }
}
