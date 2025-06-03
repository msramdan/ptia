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

        foreach ($respondenList as $responden) {
            $kuesionerList = DB::table('project_kuesioner')
                ->where('project_id', $responden->project_id)
                ->where('remark', 'Atasan')
                ->get();

            $isRespondenSuccess = true;

            DB::beginTransaction();

            try {
                // Insert jawaban kuesioner atasan (copy dari alumni)
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
                            'project_jawaban_kuesioner.nilai_delta'
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
                        ->update(['status_pengisian_kuesioner_atasan' => 'Sudah']);

                    // Ambil data skor alumni
                    $skorAlumni = DB::table('project_skor_responden')
                        ->where('project_id', $responden->project_id)
                        ->where('project_responden_id', $responden->id)
                        ->first();

                    if ($skorAlumni) {
                        // Jika data sudah ada, update bagian atasan dengan data alumni
                        DB::table('project_skor_responden')
                            ->where('id', $skorAlumni->id)
                            ->update([
                                'log_data_atasan' => $skorAlumni->log_data_alumni,
                                'skor_level_3_atasan' => $skorAlumni->skor_level_3_alumni,
                                'skor_level_4_atasan' => $skorAlumni->skor_level_4_alumni,
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Jika data belum ada, buat baru dengan data alumni untuk bagian atasan
                        DB::table('project_skor_responden')->insert([
                            'project_id' => $responden->project_id,
                            'project_responden_id' => $responden->id,
                            'log_data_atasan' => null, // Tidak ada data alumni untuk dicopy
                            'skor_level_3_atasan' => 0,
                            'skor_level_4_atasan' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $successCount++;
                } else {
                    $failCount++;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $failCount++;
                \Log::error("Error processing responden {$responden->id}: " . $e->getMessage());
            }
        }

        $endTime = now();
        sendNotifTelegram("✅ Cron Job Selesai\nBerhasil Create {$successCount} Responden, Gagal Create {$failCount} Responden\n📅 Waktu Selesai: {$endTime}", 'Cron');

        return response()->json(['status' => true, 'message' => "Proses selesai."]);
    }
}
