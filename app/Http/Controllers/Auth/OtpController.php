<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function showVerifyForm()
    {
        if (!request()->query('user_id')) {
            return redirect()->route('login')->withErrors('Sesi verifikasi tidak valid. Silakan login kembali.');
        }
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp'     => 'required|numeric|digits:6',
        ]);

        $user = User::find($request->user_id);

        if (!$user || !$user->otp_code || !$user->otp_expires_at) {
            return back()->withErrors(['otp' => 'Sesi OTP tidak valid atau kedaluwarsa.']);
        }

        // --- PERBANDINGAN STRING DENGAN STRING (PALING AMAN & SEDERHANA) ---
        $isExpired = date('Y-m-d H:i:s') > $user->otp_expires_at;

        if ($user->otp_code !== $request->otp || $isExpired) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }

        Auth::login($user);

        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return redirect()->intended(config('fortify.home'));
    }
}
