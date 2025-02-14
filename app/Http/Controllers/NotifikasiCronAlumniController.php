<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class NotifikasiCronAlumniController extends Controller
{
    public function kirimNotifikasi()
    {

        $currentHour = Carbon::now()->hour;
        if ($currentHour < 7 || $currentHour >= 17) {
            Log::info("Cron job dihentikan karena di luar jam kerja (07:00 - 17:00).");
            return response()->json(['message' => 'Di luar jam kerja, cron tidak dieksekusi.'], 200);
        }

        $startTime = Carbon::now()->format('Y-m-d H:i:s');
        $this->sendNotifTelegram("🚀 *Cron Job Dimulai* \n📅 Waktu Mulai: *{$startTime}* \nMengirim notifikasi ke alumni...");
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
            $this->sendNotifTelegram("⚠️ *Cron Job Selesai* \nTidak ada notifikasi yang dikirim. \n📅 Waktu Selesai: *{$endTime}*");
            return response()->json(['message' => 'Tidak ada notifikasi untuk dikirim.'], 200);
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($notifikasiList as $notifikasi) {
            try {
                $response = $this->sendNotifWa($notifikasi->telepon, "Halo, jangan lupa mengisi kuesioner alumni!");
                if ($response['status'] === 'success') {
                    $this->updateStatus($notifikasi->id, $notifikasi->try_send_wa_alumni, 'Alumni');
                    $successCount++;
                    $encryptedId = encryptShort($notifikasi->id);
                    $encryptedTarget = encryptShort('alumni');
                    $url = URL::to(route('responden-kuesioner.index', ['id' => $encryptedId, 'target' => $encryptedTarget]));
                    $this->sendNotifTelegram(
                        "✅ *Sukses Kirim WA* \n" .
                            "────────────\n" .
                            "👤 *Nama:* {$notifikasi->nama} \n" .
                            "📞 *Nomor:* {$notifikasi->telepon} \n" .
                            "📌 *ID Diklat:* {$notifikasi->kaldikID} \n" .
                            "📚 *Nama Diklat:* {$notifikasi->kaldikDesc} \n" .
                            "🌐 *URL Kuesioner:* [Klik di sini]({$url})\n" .
                            "────────────"
                    );
                } else {
                    $errorMessage =
                        "❌ *Gagal Kirim WA*\n" .
                        "────────────\n" .
                        "👤 *Nama:* {$notifikasi->nama}\n" .
                        "📞 *Nomor:* {$notifikasi->telepon}\n" .
                        "📌 *ID Diklat:* {$notifikasi->kaldikID}\n" .
                        "📚 *Nama Diklat:* {$notifikasi->kaldikDesc}\n" .
                        "⚠️ *Error:* {$response['message']}\n" .
                        "────────────";
                    Log::error($errorMessage);
                    $this->sendNotifTelegram($errorMessage);
                    $failureCount++;
                }
            } catch (\Exception $e) {
                $errorMessage =
                    "❌ *Terjadi Kesalahan*\n" .
                    "────────────\n" .
                    "👤 *Nama:* {$notifikasi->nama}\n" .
                    "📞 *Nomor:* {$notifikasi->telepon}\n" .
                    "📌 *ID Diklat:* {$notifikasi->kaldikID}\n" .
                    "📚 *Nama Diklat:* {$notifikasi->kaldikDesc}\n" .
                    "⚠️ *Error:* {$e->getMessage()}\n" .
                    "────────────";

                Log::error($errorMessage);
                $this->sendNotifTelegram($errorMessage);
                $failureCount++;
            }
        }

        $endTime = Carbon::now()->format('Y-m-d H:i:s');
        $this->sendNotifTelegram("✅ *Cron Job Selesai* \nNotifikasi berhasil dikirim: {$successCount} sukses, {$failureCount} gagal. \n📅 Waktu Selesai: *{$endTime}*");

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

    private function sendNotifWa($nomor, $pesan)
    {
        return ['status' => 'success', 'message' => 'Pesan berhasil dikirim'];
    }

    private function sendNotifTelegram($message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID_ALUMNI');

        if (!$botToken || !$chatId) {
            Log::error('Bot Token atau Chat ID Telegram tidak ditemukan.');
            return;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }
}
