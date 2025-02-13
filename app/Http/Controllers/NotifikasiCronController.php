<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NotifikasiCronController extends Controller
{
    public function kirimNotifikasiAlumni()
    {
        var_dump('sini');
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


        foreach ($notifikasiList as $notifikasi) {
            try {
                $response = $this->sendNotifWa($notifikasi->telepon, "Halo, jangan lupa mengisi kuesioner alumni!");
                if ($response['status'] === 'success') {
                    $this->updateStatus($notifikasi->id, $notifikasi->try_send_wa_alumni);
                    foreach ($notifikasiList as $notifikasi) {
                        $url = 'https://www.dummyurl.com';
                        $this->sendNotifTelegram("✅ *Sukses Kirim WA* \nNama: {$notifikasi->nama} \nNomor: {$notifikasi->telepon} \nID Diklat: {$notifikasi->kaldikID} \nNama Diklat: {$notifikasi->kaldikDesc} \nURL: {$url}");
                    }

                } else {
                    $errorMessage = "❌ *Gagal Kirim WA*\nNama: {$notifikasi->nama}\n📞 Nomor: {$notifikasi->telepon}\nID Diklat: {$notifikasi->kaldikID}\nNama Diklat: {$notifikasi->kaldikDesc}\n📝 Error: {$response['message']}";
                    Log::error($errorMessage);
                    $this->sendNotifTelegram($errorMessage);
                }
            } catch (\Exception $e) {
                $errorMessage = "❌ *Terjadi Kesalahan*\nNama: {$notifikasi->nama}\n📞 Nomor: {$notifikasi->telepon}\nID Diklat: {$notifikasi->kaldikID}\nNama Diklat: {$notifikasi->kaldikDesc}\nError: {$e->getMessage()}";
                Log::error($errorMessage);
                $this->sendNotifTelegram($errorMessage);
            }
        }

        $endTime = Carbon::now()->format('Y-m-d H:i:s');
        $this->sendNotifTelegram("✅ *Cron Job Selesai* \nNotifikasi berhasil dikirim. \n📅 Waktu Selesai: *{$endTime}*");
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
