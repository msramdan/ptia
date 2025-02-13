<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NotifikasiCronController extends Controller
{
    public function kirimNotifikasi()
    {
        // Kirim notifikasi Telegram saat cron dimulai
        $this->sendNotifTelegram("🚀 *Cron Job Dimulai* \nMengirim notifikasi ke alumni...");

        $notifikasiList = DB::table('project_responden')
            ->where('status_pengisian_kuesioner_alumni', 'Belum')
            ->where('try_send_wa_alumni', '<', 7)
            ->where(function ($query) {
                $query->whereNull('last_send_alumni_at')
                    ->orWhereDate('last_send_alumni_at', '<', Carbon::today());
            })
            ->orderBy('last_send_alumni_at', 'asc')
            ->limit(30)
            ->get();

        if ($notifikasiList->isEmpty()) {
            $this->sendNotifTelegram("ℹ️ *Cron Job Selesai* \nTidak ada notifikasi yang dikirim.");
            return response()->json(['message' => 'Tidak ada notifikasi untuk dikirim.'], 200);
        }

        foreach ($notifikasiList as $notifikasi) {
            try {
                $response = $this->sendNotifWa($notifikasi->nomor_wa, "Halo, jangan lupa mengisi kuesioner alumni!");
                if ($response['status'] === 'success') {
                    $this->updateStatus($notifikasi->id, $notifikasi->try_send_wa_alumni);
                    $this->sendNotifTelegram("✅ *Sukses Kirim WA*\n📞 Nomor: {$notifikasi->nomor_wa}\nPesan: {$response['message']}");
                } else {
                    $errorMessage = "❌ *Gagal Kirim WA*\n📞 Nomor: {$notifikasi->nomor_wa}\n📝 Error: {$response['message']}";
                    Log::error($errorMessage);
                    $this->sendNotifTelegram($errorMessage);
                }
            } catch (\Exception $e) {
                // Jika terjadi error, log error dan lanjutkan ke data berikutnya
                $errorMessage = "❌ *Terjadi Kesalahan*\n📞 Nomor: {$notifikasi->nomor_wa}\nError: {$e->getMessage()}";
                Log::error($errorMessage);
                $this->sendNotifTelegram($errorMessage);
            }
        }

        // Kirim notifikasi Telegram saat cron selesai
        $this->sendNotifTelegram("✅ *Cron Job Selesai* \nNotifikasi berhasil dikirim.");

        return response()->json(['message' => 'Notifikasi berhasil dikirim.'], 200);
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
        // Simulasi kirim WhatsApp (ganti dengan API WA yang sebenarnya)
        return ['status' => 'success', 'message' => 'Pesan berhasil dikirim'];
    }

    private function sendNotifTelegram($message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

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
