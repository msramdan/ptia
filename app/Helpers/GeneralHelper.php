<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

function is_active_submenu(string|array $route): string
{
    $activeClass = ' submenu-open';

    if (is_string($route)) {
        if (request()->is(substr($route . '*', 1))) {
            return $activeClass;
        }

        if (request()->is(str($route)->slug() . '*')) {
            return $activeClass;
        }

        if (request()->segment(2) === str($route)->before('/')) {
            return $activeClass;
        }

        if (request()->segment(3) === str($route)->after('/')) {
            return $activeClass;
        }
    }

    if (is_array($route)) {
        foreach ($route as $value) {
            $actualRoute = str($value)->remove(' view')->plural();

            if (request()->is(substr($actualRoute . '*', 1))) {
                return $activeClass;
            }

            if (request()->is(str($actualRoute)->slug() . '*')) {
                return $activeClass;
            }

            if (request()->segment(2) === $actualRoute) {
                return $activeClass;
            }

            if (request()->segment(3) === $actualRoute) {
                return $activeClass;
            }
        }
    }

    return '';
}

function get_setting()
{
    return DB::table('setting')->first();
}

function encryptShort($string)
{
    return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
}

function decryptShort($string)
{
    $decoded = base64_decode(strtr($string, '-_', '+/'), true);

    if ($decoded === false) {
        abort(404);
    }

    return $decoded;
}

function sendNotifTelegram($message, $remark)
{
    $botToken = env('TELEGRAM_BOT_TOKEN');
    $chatId = null;

    if ($remark === 'Alumni') {
        $chatId = env('TELEGRAM_CHAT_ID_ALUMNI', '-1002353782295');
    } elseif ($remark === 'Atasan') {
        $chatId = env('TELEGRAM_CHAT_ID_ATASAN', '-1002441723360');
    } elseif ($remark === 'Cron') {
        $chatId = env('TELEGRAM_CHAT_AUTO_CREATE', '-1002441723360');
    }

    if (!$botToken || !$chatId) {
        Log::error("Bot Token atau Chat ID Telegram tidak ditemukan untuk remark: {$remark}");
        return;
    }

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    Http::post($url, [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'Markdown',
    ]);
}

function generateMessage($notifikasi, $status, $url = null, $errorMessage = null, $remark = null)
{
    if ($remark === 'Atasan') {
        return implode("\n", [
            $status ? "✅ *Sukses Kirim WA*" : "❌ *Gagal Kirim WA*",
            "────────────",
            "👨‍💼 *Nama Atasan:* {$notifikasi->nama_atasan}",
            "📞 *Nomor Atasan:* {$notifikasi->telepon_atasan}",
            "👤 *Nama Peserta:* {$notifikasi->nama}",
            "📌 *ID Diklat:* {$notifikasi->kaldikID}",
            "📚 *Nama Diklat:* {$notifikasi->kaldikDesc}",
            $status ? "🌐 *URL Kuesioner Atasan:* [Klik di sini]({$url})" : "⚠️ *Error:* {$errorMessage}",
            "────────────"
        ]);
    } elseif ($remark === 'Alumni') {
        return implode("\n", [
            $status ? "✅ *Sukses Kirim WA*" : "❌ *Gagal Kirim WA*",
            "────────────",
            "👤 *Nama:* {$notifikasi->nama}",
            "📞 *Nomor:* {$notifikasi->telepon}",
            "📌 *ID Diklat:* {$notifikasi->kaldikID}",
            "📚 *Nama Diklat:* {$notifikasi->kaldikDesc}",
            $status ? "🌐 *URL Kuesioner Alumni:* [Klik di sini]({$url})" : "⚠️ *Error:* {$errorMessage}",
            "────────────"
        ]);
    }

    return "⚠️ *Remark tidak valid!*";
}

function formatPesanWhatsApp($html, $notifikasi = null, $remark = null)
{
    // Ubah <p> menjadi newline, mengganti </p><p> dengan dua newline
    $text = preg_replace('/<\/p>\s*<p>/', "\n\n", $html);

    // Hapus tag <p> di awal dan akhir
    $text = preg_replace('/<\/?p>/', '', $text);

    // Konversi teks tebal <b> atau <strong> menjadi *
    $text = preg_replace('/<b>(.*?)<\/b>|<strong>(.*?)<\/strong>/', '*$1$2*', $text);

    // Konversi teks miring <i> atau <em> menjadi _
    $text = preg_replace('/<i>(.*?)<\/i>|<em>(.*?)<\/em>/', '_$1$2_', $text);

    // Konversi teks bergaris bawah <u> menjadi ~
    $text = preg_replace('/<u>(.*?)<\/u>/', '~$1~', $text);

    // Ubah <br> menjadi newline
    $text = preg_replace('/<br\s*\/?>/i', "\n", $text);

    // Decode HTML entities (&nbsp;, &lt;, dll.)
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    // Hapus tag HTML lainnya jika masih ada
    $text = strip_tags($text);

    // Ganti {params_link} jika ada
    if (strpos($text, '{params_link}') !== false && $notifikasi && $remark) {
        $encryptedId = encryptShort($notifikasi->id);
        $encryptedTarget = encryptShort($remark);
        $url = URL::to(route('responden-kuesioner.index', ['id' => $encryptedId, 'target' => $encryptedTarget]));

        $text = str_replace('{params_link}', $url, $text);
    }

    // Ganti {params_deadline} jika ada
    if (strpos($text, '{params_deadline}') !== false && $notifikasi && $remark) {
        $deadline = null;

        if ($remark === 'Alumni') {
            $deadline = $notifikasi->deadline_pengisian_alumni ?? '-';
        } elseif ($remark === 'Atasan') {
            $deadline = $notifikasi->deadline_pengisian_atasan ?? '-';
        }

        $text = str_replace('{params_deadline}', $deadline, $text);
    }

    // Trim untuk menghapus spasi ekstra di awal & akhir
    return trim($text);
}

function sendNotifWa($notifikasi, $nomor, $remark)
{
    $session = DB::table('sessions')
        ->where('status', 'CONNECTED')
        ->where('is_aktif', 'Yes')
        ->select('api_key')
        ->first();

    if (!$session) {
        return ['status' => false, 'message' => 'Gagal mengirim pesan: Tidak ada session aktif yang terhubung'];
    }

    $apiKey = $session->api_key;
    $baseNode = env('BASE_NODE', 'http://localhost:3301');
    $url = "$baseNode/api/send-message";
    $timeout = env('TIME_OUT_SEND_WA', 5);

    // Ambil data dari tabel project_pesan_wa berdasarkan project_id
    $pesanData = DB::table('project_pesan_wa')->where('project_id', $notifikasi->project_id)->first();

    // Jika data tidak ditemukan, return error
    if (!$pesanData) {
        return ['status' => false, 'message' => 'Pesan tidak ditemukan untuk project ini.'];
    }

    // Tentukan pesan berdasarkan remark
    if ($remark == "Alumni") {
        $message = $pesanData->text_pesan_alumni ?? null;
    } elseif ($remark == "Atasan") {
        $message = $pesanData->text_pesan_atasan ?? null;
    } else {
        return ['status' => false, 'message' => 'Tipe remark tidak valid.'];
    }

    // Jika pesan kosong, return error
    if (!$message) {
        return ['status' => false, 'message' => 'Pesan tidak tersedia untuk remark ini.'];
    }

    // Format pesan ke format WhatsApp dengan mengganti {params_link}
    $message = formatPesanWhatsApp($message, $notifikasi, $remark);

    // Cek jika nomor kosong atau tidak valid (harus angka & panjang 10-15 karakter)
    if (empty($nomor) || !preg_match('/^(0|62)[0-9]{9,14}$/', $nomor)) {
        return ['status' => false, 'message' => 'Invalid WhatsApp number.'];
    }

    // Ubah nomor jika diawali dengan "0"
    if (substr($nomor, 0, 1) === "0") {
        $nomor = "62" . substr($nomor, 1);
    }

    $payload = [
        "api_key" => (string) $apiKey,
        "receiver" => (string) $nomor,
        "data" => [
            "message" => (string) $message
        ]
    ];

    try {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new Exception("cURL initialization failed");
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errorMsg = curl_error($ch);
            curl_close($ch);

            if (strpos($errorMsg, "timed out") !== false) {
                return ['status' => false, 'message' => 'Gagal mengirim pesan: Permintaan timeout'];
            }

            throw new Exception('cURL error: ' . $errorMsg);
        }

        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        if ($httpCode == 200 && isset($decodedResponse['status']) && $decodedResponse['status'] === true) {
            return ['status' => true, 'message' => 'Pesan berhasil dikirim', 'response' => $decodedResponse];
        } else {
            return ['status' => false, 'message' => $decodedResponse['message'] ?? 'Gagal mengirim pesan', 'error' => $decodedResponse];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Gagal mengirim pesan', 'error' => $e->getMessage()];
    }
}

if (!function_exists('formatTanggalIndonesia')) {
    function formatTanggalIndonesia($tanggal)
    {
        Carbon::setLocale('id');
        return Carbon::parse($tanggal)->translatedFormat('d F Y');
    }
}
