<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

class NotifikasiCronAlumniController extends Controller
{
    public function kirimNotifikasi()
    {
        $type = "Alumni";
        $currentHour = Carbon::now()->hour;
        $currentDay = Carbon::now()->dayOfWeek;

        // Cek apakah waktu di luar jam kerja atau hari Sabtu/Minggu
        // if ($currentHour < 7 || $currentHour >= 17 || $currentDay === 0 || $currentDay === 6) {
        //     Log::info("Cron job dihentikan karena di luar jam kerja atau hari libur (Sabtu/Minggu).");
        //     return response()->json(['message' => 'Di luar jam kerja atau hari libur, cron tidak dieksekusi.'], 200);
        // }

        $startTime = Carbon::now()->format('Y-m-d H:i:s');
        sendNotifTelegram("🚀 *Cron Job Dimulai* \n📅 Waktu Mulai: *{$startTime}* \nMengirim notifikasi ke alumni...", $type);
        $limit = (int) env('NOTIFIKASI_LIMIT', 10);
        $notifikasiList = DB::table('project_responden')
            ->join('project_pesan_wa', 'project_responden.project_id', '=', 'project_pesan_wa.project_id')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->where('project_responden.status_pengisian_kuesioner_alumni', 'Belum')
            ->where('project_responden.try_send_wa_alumni', '<', 7)
            ->where(function ($query) {
                $query->whereNull('project_responden.last_send_alumni_at')
                    ->orWhereDate('project_responden.last_send_alumni_at', '<', Carbon::today());
            })
            ->orderBy('project_responden.last_send_alumni_at', 'asc')
            ->limit($limit)
            ->select(
                'project_responden.*',
                'project_pesan_wa.text_pesan_alumni',
                'project.status',
                'project.kaldikID',
                'project.kaldikDesc'
            )
            ->get();

        if ($notifikasiList->isEmpty()) {
            $endTime = Carbon::now()->format('Y-m-d H:i:s');
            sendNotifTelegram("⚠️ *Cron Job Selesai* \nTidak ada notifikasi yang dikirim. \n📅 Waktu Selesai: *{$endTime}*", $type);
            return response()->json(['message' => 'Tidak ada notifikasi untuk dikirim.'], 200);
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($notifikasiList as $notifikasi) {
            try {
                $response =sendNotifWa($notifikasi->telepon, "Halo, jangan lupa mengisi kuesioner alumni!", $type);
                $status = $response['status'];
                $statusText = $status ? 'Sukses' : 'Gagal';
                // Insert ke tabel log
                DB::table('project_log_send_notif')->insert([
                    'telepon' => $notifikasi->telepon,
                    'remark' => "Alumni",
                    'status' => $statusText,
                    'project_responden_id' => $notifikasi->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->updateStatus($notifikasi->id, $notifikasi->try_send_wa_alumni);
                if ($status) {
                    $successCount++;
                    $encryptedId = encryptShort($notifikasi->id);
                    $encryptedTarget = encryptShort($type);
                    $url = URL::to(route('responden-kuesioner.index', ['id' => $encryptedId, 'target' => $encryptedTarget]));
                }

                $message = generateMessage($notifikasi, $status, $url ?? null, $response['message'] ?? null, $type);

                if (!$status) {
                    Log::error($message);
                    $failureCount++;
                }
                sendNotifTelegram($message, $type);
            } catch (\Exception $e) {
                $this->updateStatus($notifikasi->id, $notifikasi->try_send_wa_alumni);
                // Insert ke tabel log jika terjadi error
                DB::table('project_log_send_notif')->insert([
                    'telepon' => $notifikasi->telepon,
                    'remark' => "Alumni",
                    'status' => 'Gagal',
                    'project_responden_id' => $notifikasi->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $errorMessage = generateMessage($notifikasi, false, null, $e->getMessage(), $type);
                Log::error($errorMessage);
                sendNotifTelegram($errorMessage, $type);
                $failureCount++;
            }
        }

        $endTime = Carbon::now()->format('Y-m-d H:i:s');
        sendNotifTelegram("✅ *Cron Job Selesai* \nNotifikasi berhasil dikirim: {$successCount} sukses, {$failureCount} gagal. \n📅 Waktu Selesai: *{$endTime}*", $type);

        return response()->json([
            'message' => 'Notifikasi berhasil dikirim.',
            'success_count' => $successCount,
            'failure_count' => $failureCount
        ], 200);
    }

    private function updateStatus($id, $trySendCount)
    {
        DB::table('project_responden')
            ->where('id', $id)
            ->update([
                'try_send_wa_alumni' => $trySendCount + 1,
                'last_send_alumni_at' => Carbon::now(),
            ]);
    }
}
