<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class LoginController extends Controller
{
    /**
     * Menangani permintaan login awal dan mengirim OTP.
     * Didesain untuk AJAX request.
     */
    public function loginAndSendOtp(Request $request)
    {

        $request->validate([
            'username' => 'required|string',

        ]);

        // 2. Logika untuk mencari user berdasarkan username atau email
        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $user = User::where($loginField, $request->username)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna dengan ' . $loginField . ' tersebut tidak ditemukan.'
            ], 404); // Menggunakan status 404 Not Found lebih tepat
        }

        // Pastikan user punya email untuk dikirimi OTP
        if (empty($user->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini tidak memiliki alamat email terdaftar untuk pengiriman OTP.'
            ], 422);
        }

        // Buat dan simpan OTP ke cache
        $otpCode = rand(100000, 999999);
        // Ganti kunci cache agar lebih sesuai
        $cacheKey = 'otp_for_user_' . $user->id;

        $expireInMinutes = config('otp.expire', 5);
        Cache::put($cacheKey, $otpCode, now()->addMinutes($expireInMinutes));

        // Kirim email
        try {
            Mail::to($user->email)->send(new SendOtpMail($otpCode, $expireInMinutes));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email OTP. ' . $e->getMessage()
            ], 500);
        }

        // Jika berhasil, kirim response sukses
        return response()->json([
            'success' => true,
            'message' => 'OTP telah dikirim ke email Anda.',
            'user_id' => $user->id
        ]);
    }

    /**
     * Memverifikasi OTP dari modal.
     * Didesain untuk AJAX request.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|numeric|digits:6',
        ]);

        $cacheKey = 'otp_for_user_' . $request->user_id;
        $storedOtp = Cache::get($cacheKey);


        if (!$storedOtp) {
            return response()->json(['success' => false, 'message' => 'OTP sudah kedaluwarsa.'], 422);
        }

        if ($storedOtp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP tidak valid.'], 422);
        }

        // Jika OTP benar, loginkan user
        $user = User::find($request->user_id);
        Auth::login($user);

        Cache::forget($cacheKey); // Hapus OTP dari cache

        return response()->json([
            'success' => true,
            'redirect_url' => route('dashboard')
        ]);
    }

    /**
     * Menangani permintaan kirim ulang OTP.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if (!$user || !$user->email) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat mengirim ulang OTP untuk pengguna ini.'], 422);
        }

        // Buat dan simpan OTP baru
        $otpCode = rand(100000, 999999);
        $cacheKey = 'otp_for_user_' . $user->id;
        $expireInMinutes = config('otp.expire', 5);
        Cache::put($cacheKey, $otpCode, now()->addMinutes($expireInMinutes));

        // Kirim ulang email
        try {
            Mail::to($user->email)->send(new SendOtpMail($otpCode, $expireInMinutes));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email OTP. ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP baru telah dikirim ulang ke email Anda.'
        ]);
    }
}
