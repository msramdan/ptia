<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class VerifyOtpController extends Controller
{
    /**
     * Memverifikasi OTP yang dikirim via AJAX.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp'     => 'required|numeric|digits:6',
        ]);

        $userId = $request->user_id;
        $user = User::find($userId);

        $otpKey = 'otp_' . $userId;
        $storedOtp = Cache::get($otpKey);

        if ($storedOtp != $request->otp) {
            return response()->json(['message' => 'Kode OTP yang Anda masukkan salah.'], 422);
        }

        // Jika OTP benar, login-kan user
        Auth::login($user);

        // Hapus cache dan session OTP
        Cache::forget($otpKey);
        session()->regenerate();

        return response()->json([
            'success' => true,
            'redirect_url' => config('fortify.home'),
        ]);
    }

    /**
     * Mengirim ulang kode OTP via AJAX.
     */
    public function resend(Request $request)
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return response()->json(['message' => 'Sesi Anda telah berakhir, silakan muat ulang halaman dan login kembali.'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
        }

        try {
            $otp = rand(100000, 999999);
            $otpExpiration = (int)config('otp.expired_otp', 3);

            Cache::put('otp_' . $user->id, $otp, now()->addMinutes($otpExpiration));
            Mail::to($user->email)->send(new SendOtpMail((string)$otp));

            return response()->json(['message' => 'Kode OTP baru telah berhasil dikirim.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengirim ulang OTP.'], 500);
        }
    }
}
