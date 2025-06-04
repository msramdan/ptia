<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\Setting;

class NotifikasiCronAtasanController extends Controller
{

    public function kirimNotifikasi()
    {
        $type = "Atasan";
        $setting = Setting::first();

        if (!$setting) {
            Log::error("Pengaturan CRON tidak ditemukan di database.");
            return response()->json(['message' => 'Pengaturan CRON tidak ditemukan.'], 404);
        }

        if ($setting->cron_notif_atasan !== 'Yes') {
            sendNotifTelegram("❌ Notifikasi atasan dinonaktifkan di pengaturan.", $type);
            return response()->json(['message' => 'Notifikasi atasan dinonaktifkan di pengaturan.'], 200);
        }

        $workStartHour = Carbon::createFromFormat('H:i:s', $setting->jam_mulai)->hour;
        $workEndHour = Carbon::createFromFormat('H:i:s', $setting->jam_selesai)->hour;
        $activeDays = is_string($setting->hari_jalan_cron)
            ? json_decode($setting->hari_jalan_cron, true)
            : $setting->hari_jalan_cron;

        $currentHour = Carbon::now()->hour;
        $currentDay = Carbon::now()->dayOfWeek;
        if ($currentHour < $workStartHour || $currentHour >= $workEndHour || !in_array($currentDay, $activeDays)) {
            Log::info("Cron job dihentikan: di luar jam kerja atau hari tidak aktif.");
            return response()->json(['message' => 'Di luar jam kerja atau bukan hari kerja, cron tidak dieksekusi.'], 200);
        }

        $startTime = Carbon::now()->format('Y-m-d H:i:s');
        sendNotifTelegram("🚀 *Cron Job Dimulai* \n📅 Waktu Mulai: *{$startTime}* \nMengirim notifikasi ke atasan...", $type);
        $limit = (int) env('NOTIFIKASI_LIMIT', 10);
        $notifikasiList = DB::table('project_responden')
            ->join('project_pesan_wa', 'project_responden.project_id', '=', 'project_pesan_wa.project_id')
            ->join('project', 'project_responden.project_id', '=', 'project.id')
            ->where('project.status', 'Pelaksanaan')
            ->where('project.send_notif_project_atasan', 'Yes')
            ->where('project_responden.send_notif_atasan', 'Yes')
            ->where('project_responden.status_pengisian_kuesioner_alumni', 'Sudah')
            ->where('project_responden.status_pengisian_kuesioner_atasan', 'Belum')
            ->where('project_responden.try_send_wa_atasan', '<', 7)
            ->whereNotNull('project_responden.telepon_atasan')
            ->where(function ($query) {
                $query->whereNull('project_responden.last_send_atasan_at')
                    ->orWhereDate('project_responden.last_send_atasan_at', '<', Carbon::today());
            })
            ->orderBy('project_responden.last_send_atasan_at', 'asc')
            ->limit($limit)
            ->select(
                'project_responden.*',
                'project_pesan_wa.text_pesan_atasan',
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
                $response = sendNotifWa($notifikasi, $notifikasi->telepon_atasan, $type);
                $status = $response['status'];
                $statusText = $status ? 'Sukses' : 'Gagal';

                // Insert ke tabel log
                DB::table('project_log_send_notif')->insert([
                    'telepon' => $notifikasi->telepon_atasan,
                    'remark' => "Atasan",
                    'status' => $statusText,
                    'log_pesan' => isset($response['message']) ? $response['message'] : null,
                    'project_responden_id' => $notifikasi->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->updateStatus($notifikasi->id, $notifikasi->try_send_wa_atasan);
                if ($status) {
                    $successCount++;
                    $encryptedId = encryptShort($notifikasi->id);
                    $encryptedTarget = encryptShort($type);
                    $url = URL::to(route('responden-kuesioner.index', [
                        'id'     => $encryptedId,
                        'target' => $encryptedTarget,
                        'token'  => $notifikasi->token
                    ]));
                }

                $message = generateMessage($notifikasi, $status, $url ?? null, $response['message'] ?? null, $type);

                if (!$status) {
                    Log::error($message);
                    $failureCount++;
                }

                if (env('SEND_NOTIF_TELEGRAM', false)) {
                    sendNotifTelegram($message, $type);
                }
            } catch (\Exception $e) {
                $this->updateStatus($notifikasi->id, $notifikasi->try_send_wa_atasan);
                // Insert ke tabel log
                DB::table('project_log_send_notif')->insert([
                    'telepon' => $notifikasi->telepon_atasan,
                    'remark' => "Atasan",
                    'status' => 'Gagal',
                    'log_pesan' => $e->getMessage(),
                    'project_responden_id' => $notifikasi->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $errorMessage = generateMessage($notifikasi, false, null, $e->getMessage(), $type);
                Log::error($errorMessage);
                if (env('SEND_NOTIF_TELEGRAM', false)) {
                    sendNotifTelegram($errorMessage, $type);
                }
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
                'try_send_wa_atasan' => $trySendCount + 1,
                'last_send_atasan_at' => Carbon::now(),
            ]);
    }
}
