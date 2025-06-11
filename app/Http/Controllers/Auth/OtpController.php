<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OtpController extends Controller
{
    public function showVerifyForm()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login')->withErrors(['username' => 'Sesi verifikasi tidak valid. Silakan login kembali.']);
        }
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|numeric|digits:6']);

        $userId = session('otp_user_id');
        if (!$userId) {
            return back()->withErrors(['otp' => 'Sesi Anda telah berakhir.']);
        }

        $otpKey = 'otp_' . $userId;
        $storedOtp = Cache::get($otpKey);

        if ($storedOtp != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        $user = User::find($userId);
        Auth::login($user);

        Cache::forget($otpKey);
        session()->forget('otp_user_id');

        return redirect()->intended(config('fortify.home'));
    }
}
