<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\{
    Http,
};
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{
    /**
     * Menangani permintaan login awal dan mengirim OTP.
     * Didesain untuk AJAX request.
     */
    public function loginAndSendOtp(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        // Clear any existing OTP session
        session()->forget('otp_user_id');

        // Check if we should hit the Stara API
        if (config('stara.is_hit')) {
            $response = Http::post(config('stara.endpoint') . '/auth/login', [
                'username' => $request->username,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user = User::where('user_nip', $data['data']['user_info']['user_nip'])->first();

                if (!$user) {
                    $user = $this->createUser($data['data']['user_info']);
                }

                // Handle OTP flow
                if (config('otp.is_send_otp', true)) {
                    // Check if user has email
                    if (empty($user->email)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Akun ini tidak memiliki alamat email terdaftar untuk pengiriman OTP.'
                        ], 422);
                    }

                    // Generate and store OTP
                    $otpCode = rand(100000, 999999);
                    $cacheKey = 'otp_for_user_' . $user->id;
                    $expireInMinutes = (int) config('otp.expired_otp', 5);
                    Cache::put($cacheKey, $otpCode, now()->addMinutes($expireInMinutes));

                    // Send OTP email
                    try {
                        Mail::to($user->email)->send(new SendOtpMail($otpCode, $expireInMinutes));
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Gagal mengirim email OTP. ' . $e->getMessage()
                        ], 500);
                    }

                    // Store user ID in session for OTP verification
                    session(['otp_user_id' => $user->id]);

                    return response()->json([
                        'success' => true,
                        'message' => 'OTP telah dikirim ke email Anda.',
                        'user_id' => $user->id
                    ]);
                } else {
                    // OTP disabled - login directly
                    Auth::login($user);
                    return response()->json([
                        'success'      => true,
                        'redirect_url' => route('dashboard')
                    ]);
                }
            }
        } else {
            $user = $this->handleDefaultUser($request);
            if ($user) {
                Auth::login($user);
                return response()->json([
                    'success'      => true,
                    'redirect_url' => route('dashboard')
                ]);
            }
        }

        // If we get here, authentication failed
        return response()->json([
            'success' => false,
            'message' => 'Autentikasi gagal. Silakan cek kembali username dan password Anda.'
        ], 401);
    }

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

    private function createUser(array $userInfo): User
    {
        $nipLama = $userInfo['user_nip'];
        $response = Http::get(config('stara.map_endpoint') . '/v2/pegawai/sima/atlas', [
            'api_token' => config('stara.map_api_token_employee'),
            's_nip' => $nipLama,
        ]);

        if ($response->successful()) {
            $kodeEselon2 = $response->json()['result'][0]['kode_eselon2'];
        } else {
            throw new \Exception('Request to unit kerja endpoint failed.');
        }

        $user = User::create([
            'user_nip' => $userInfo['user_nip'],
            'name' => $userInfo['name'],
            'phone' => $userInfo['nomor_hp'] ?? '',
            'email' => $userInfo['email'],
            'jabatan' => $userInfo['jabatan'],
            'kode_unit' => $kodeEselon2,
            'nama_unit' => $userInfo['namaunit'],
        ]);

        $this->assignRole($user, 2);
        return $user;
    }

    private function assignRole(User $user, int $roleId): void
    {
        $role = Role::find($roleId);
        if ($role) {
            $user->assignRole($role);
        } else {
            throw new \Exception('Role not found');
        }
    }

    private function handleDefaultUser($request)
    {
        $user = User::firstOrCreate(
            ['name' => $request->username],
            [
                'user_nip' => $this->generateRandomNip(),
                'phone' => '-',
                'email' => $this->generateRandomEmail(),
                'jabatan' => '-',
                'nama_unit' => '-'
            ]
        );

        $this->assignRole($user, 1);
        return $user;
    }

    function generateRandomEmail()
    {
        $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
        $domain = 'gmail.com';
        return $randomString . '@' . $domain;
    }

    function generateRandomNip()
    {
        return str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
