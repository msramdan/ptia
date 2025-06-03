<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;


class AutoInsertKuesionerAtasanController extends Controller
{

    public function insertData()
    {
        $startTime = now();
        sendNotifTelegram("🚀 Cron Job Auto Insert Kuesioner Atasan Expired Dimulai\n📅 Waktu Mulai: {$startTime}", 'Cron');

        // Ambil daftar responden yang deadline pengisian atasan sudah lewat dan belum diisi
        $respondenList = DB::table('project_responden')
            ->whereDate('deadline_pengisian_atasan', '<', Carbon::now()->toDateString())
            ->where('status_pengisian_kuesioner_atasan', 'Belum')
            ->limit(30)
            ->get();

        if ($respondenList->isEmpty()) {
            $endTime = now();
            sendNotifTelegram("⚠️ Cron Job Selesai\nTidak ada data yang diproses\n📅 Waktu Selesai: {$endTime}", 'Cron');
            return response()->json(['status' => true, 'message' => "Tidak ada data yang diproses."]);
        }

        $successCount = 0;
        $failCount = 0;
        $processedRespondenIds = [];
        $failedRespondenIds = [];

        foreach ($respondenList as $responden) {
            $kuesionerList = DB::table('project_kuesioner')
                ->where('project_id', $responden->project_id)
                ->where('remark', 'Atasan')
                ->get();

            $isRespondenSuccess = true;

            DB::beginTransaction();

            try {
                // Insert jawaban kuesioner atasan berdasarkan jawaban alumni
                foreach ($kuesionerList as $kuesioner) {
                    $jawabanAlumni = DB::table('project_jawaban_kuesioner')
                        ->join('project_kuesioner', 'project_kuesioner.id', '=', 'project_jawaban_kuesioner.project_kuesioner_id')
                        ->where('project_kuesioner.aspek_id', $kuesioner->aspek_id)
                        ->where('project_kuesioner.project_id', $responden->project_id)
                        ->where('project_jawaban_kuesioner.project_responden_id', $responden->id)
                        ->where('project_jawaban_kuesioner.remark', 'Alumni')
                        ->select(
                            'project_jawaban_kuesioner.nilai_sebelum',
                            'project_jawaban_kuesioner.nilai_sesudah',
                            'project_jawaban_kuesioner.nilai_delta',
                            'project_kuesioner.aspek_id',
                            'project_kuesioner.kriteria',
                            'project_kuesioner.level'
                        )
                        ->first();

                    if ($jawabanAlumni) {
                        $insertResult = DB::table('project_jawaban_kuesioner')->insert([
                            'project_kuesioner_id' => $kuesioner->id,
                            'project_responden_id' => $responden->id,
                            'nilai_sebelum' => $jawabanAlumni->nilai_sebelum,
                            'nilai_sesudah' => $jawabanAlumni->nilai_sesudah,
                            'nilai_delta' => $jawabanAlumni->nilai_delta,
                            'catatan' => null,
                            'remark' => 'Atasan',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        if (!$insertResult) {
                            $isRespondenSuccess = false;
                        }
                    } else {
                        $isRespondenSuccess = false;
                    }
                }

                if ($isRespondenSuccess) {
                    // Update status responden
                    DB::table('project_responden')
                        ->where('id', $responden->id)
                        ->update([
                            'status_pengisian_kuesioner_atasan' => 'Sudah',
                            'insert_from_cron' => 'Yes',
                        ]);
                    // Proses untuk project_skor_responden
                    $diklatTypeId = DB::table('project')
                        ->where('id', $responden->project_id)
                        ->value('diklat_type_id');

                    // Ambil semua jawaban atasan yang baru diinsert
                    $jawabanAtasan = DB::table('project_jawaban_kuesioner')
                        ->join('project_kuesioner', 'project_kuesioner.id', '=', 'project_jawaban_kuesioner.project_kuesioner_id')
                        ->where('project_jawaban_kuesioner.project_responden_id', $responden->id)
                        ->where('project_jawaban_kuesioner.remark', 'Atasan')
                        ->select(
                            'project_kuesioner.aspek_id',
                            'project_kuesioner.kriteria',
                            'project_kuesioner.level',
                            'project_jawaban_kuesioner.nilai_delta'
                        )
                        ->get();

                    // Hitung rata-rata per aspek
                    $averageByAspek = [];
                    $countByAspek = [];
                    $kriteriaByAspek = [];
                    $levelByAspek = [];

                    foreach ($jawabanAtasan as $jawaban) {
                        $aspekId = $jawaban->aspek_id;
                        $kriteria = $jawaban->kriteria;
                        $level = $jawaban->level;

                        if ($aspekId !== null) {
                            if (!isset($averageByAspek[$aspekId])) {
                                $averageByAspek[$aspekId] = 0;
                                $countByAspek[$aspekId] = 0;
                                $kriteriaByAspek[$aspekId] = $kriteria;
                                $levelByAspek[$aspekId] = $level;
                            }
                            $averageByAspek[$aspekId] += $jawaban->nilai_delta;
                            $countByAspek[$aspekId]++;
                        }
                    }

                    // Buat array hasil rata-rata dengan konversi dan bobot
                    $averageData = [];

                    foreach ($averageByAspek as $aspekId => $totalDelta) {
                        $averageNilaiDelta = (int) round($totalDelta / $countByAspek[$aspekId]);
                        $kriteria = $kriteriaByAspek[$aspekId] ?? null;

                        // Penyesuaian kriteria untuk query konversi
                        $jenisSkor = ($kriteria == 'Delta Skor Persepsi') ? '∆ Skor Persepsi' : $kriteria;

                        // Ambil data konversi
                        $konversi = DB::table('konversi')
                            ->where('diklat_type_id', $diklatTypeId)
                            ->where('jenis_skor', $jenisSkor)
                            ->where('skor', $averageNilaiDelta)
                            ->value('konversi') ?? 0;

                        // Ambil bobot atasan
                        $bobot = DB::table('project_bobot_aspek')
                            ->where('project_id', $responden->project_id)
                            ->where('aspek_id', $aspekId)
                            ->value('bobot_atasan_langsung') ?? 0;

                        // Hitung nilai
                        $nilai = round(($konversi * $bobot) / 100, 2);

                        $averageData[] = [
                            'diklat_type_id' => $diklatTypeId,
                            'aspek_id' => $aspekId,
                            'kriteria' => $kriteria,
                            'level' => $levelByAspek[$aspekId] ?? null,
                            'average_nilai_delta' => $averageNilaiDelta,
                            'konversi' => (int) $konversi,
                            'bobot' => $bobot,
                            'nilai' => $nilai,
                        ];
                    }

                    // Hitung skor level 3 dan 4
                    $skorLevel3 = collect($averageData)
                        ->where('level', '3')
                        ->sum('nilai');

                    $skorLevel4 = collect($averageData)
                        ->where('level', '4')
                        ->sum('nilai');

                    // Cek apakah data sudah ada di project_skor_responden
                    $existingData = DB::table('project_skor_responden')
                        ->where('project_id', $responden->project_id)
                        ->where('project_responden_id', $responden->id)
                        ->first();

                    if ($existingData) {
                        // Update data yang sudah ada
                        DB::table('project_skor_responden')
                            ->where('id', $existingData->id)
                            ->update([
                                'log_data_atasan' => json_encode($averageData, JSON_PRETTY_PRINT),
                                'skor_level_3_atasan' => round($skorLevel3, 2),
                                'skor_level_4_atasan' => round($skorLevel4, 2),
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Insert data baru
                        DB::table('project_skor_responden')->insert([
                            'project_id' => $responden->project_id,
                            'project_responden_id' => $responden->id,
                            'log_data_atasan' => json_encode($averageData, JSON_PRETTY_PRINT),
                            'skor_level_3_atasan' => round($skorLevel3, 2),
                            'skor_level_4_atasan' => round($skorLevel4, 2),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $processedRespondenIds[] = $responden->id;
                    $successCount++;
                } else {
                    $failedRespondenIds[] = $responden->id;
                    $failCount++;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $failedRespondenIds[] = $responden->id;
                $failCount++;
                \Log::error("Error processing responden {$responden->id}: " . $e->getMessage());
            }
        }

        $endTime = now();

        // Format pesan notifikasi
        $message = "✅ Cron Job Selesai\n";
        $message .= "📌 Total Diproses: " . count($respondenList) . "\n";
        $message .= "✔ Berhasil: {$successCount}\n";
        $message .= "✖ Gagal: {$failCount}\n";

        if (!empty($processedRespondenIds)) {
            $message .= "🔹 Responden ID Berhasil: " . implode(', ', $processedRespondenIds) . "\n";
        }

        if (!empty($failedRespondenIds)) {
            $message .= "🔸 Responden ID Gagal: " . implode(', ', $failedRespondenIds) . "\n";
        }

        $message .= "📅 Waktu Mulai: {$startTime}\n";
        $message .= "📅 Waktu Selesai: {$endTime}\n";
        $message .= "⏳ Durasi: " . $startTime->diffInSeconds($endTime) . " detik";

        sendNotifTelegram($message, 'Cron');

        return response()->json([
            'status' => true,
            'message' => "Proses selesai.",
            'data' => [
                'total' => count($respondenList),
                'success' => $successCount,
                'failed' => $failCount,
                'processed_ids' => $processedRespondenIds,
                'failed_ids' => $failedRespondenIds
            ]
        ]);
    }
}
