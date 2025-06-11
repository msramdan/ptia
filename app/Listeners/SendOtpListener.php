<?php

namespace App\Listeners;

use App\Mail\SendOtpMail;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SendOtpListener
{
    public function handle(Login $event): void
    {
        // Cek apakah fitur OTP diaktifkan di .env
        if (config('otp.is_send_otp', false) == true) {
            $user = $event->user;

            // Buat kode OTP 6 digit
            $otp = rand(100000, 999999);
            $expires_at = now()->addMinutes(config('otp.expired_otp', 3));

            // Simpan OTP ke user
            $user->otp_code = $otp;
            $user->otp_expires_at = $expires_at;
            $user->save();

            // Kirim email
            Mail::to($user->email)->send(new SendOtpMail($otp));

            // Logout pengguna untuk sementara
            Auth::logout();

            // Arahkan ke halaman verifikasi OTP
            session()->put('url.intended', route('otp.show', ['user_id' => $user->id]));
        }
    }
}
