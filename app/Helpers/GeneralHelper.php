<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

function sendNotifWa($nomor, $pesan, $remark)
{
    return ['status' => true, 'message' => 'Pesan berhasil dikirim'];
}
