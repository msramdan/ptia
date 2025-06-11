<?php

namespace App\Providers;

use App\Actions\Fortify\{CreateNewUser, ResetUserPassword, UpdateUserPassword, UpdateUserProfileInformation};
use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\{
    Auth,
    Http,
    Config,
    Mail,
    Cache,
    RateLimiter
};
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Mail\SendOtpMail;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;


class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, function ($app) {
            return new class implements LoginResponseContract {
                public function toResponse($request)
                {
                    // Jika ada session 'otp_user_id', berarti user harus verifikasi OTP
                    if (config('otp.is_send_otp') && session()->has('otp_user_id')) {
                        // Arahkan ke halaman verifikasi OTP
                        return redirect()->route('otp.show');
                    }

                    // Jika tidak, arahkan ke dashboard seperti biasa
                    return redirect()->intended(config('fortify.home'));
                }
            };
        });
    }

    public function boot(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ]);

            // Selalu bersihkan session OTP lama saat mencoba login baru
            session()->forget('otp_user_id');

            // Logika login Anda ke API Stara...
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

                    // --- LOGIKA OTP YANG DISEMPURNAKAN ---
                    if (config('otp.is_send_otp') === true) {
                        $this->sendOtp($user); // Panggil fungsi helper untuk mengirim OTP
                        // Kembalikan user object agar Fortify tahu otentikasi password berhasil
                        // Sisanya akan ditangani oleh LoginResponse di atas
                        return $user;
                    }

                    // Jika OTP tidak aktif, langsung login
                    Auth::login($user, $request->filled('remember'));
                    return $user;
                }
            } else {
                return $this->handleDefaultUser($request);
            }

            throw ValidationException::withMessages([
                Fortify::username() => trans('auth.failed'),
            ]);
        });

        // Fortify default configurations
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower($request->input(Fortify::username())) . '|' . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });


        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::confirmPasswordView(function () {
            return view('auth.confirm-password');
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function (Request $request) {
            return view('auth.reset-password', ['request' => $request]);
        });
    }

    /**
     * Fungsi helper untuk mengirim OTP (meniru referensi Anda)
     */
    private function sendOtp(User $user): void
    {
        $otp = rand(100000, 999999);
        $otpExpiration = (int)config('otp.expired_otp', 3);

        // Simpan OTP di Cache, bukan di database
        Cache::put('otp_' . $user->id, $otp, now()->addMinutes($otpExpiration));

        try {
            Mail::to($user->email)->send(new SendOtpMail((string)$otp));
        } catch (\Exception $e) {
            // Jika gagal, gagalkan dengan pesan yang jelas
            throw ValidationException::withMessages(['username' => 'Gagal mengirim OTP: ' . $e->getMessage()]);
        }

        // Simpan ID user di session untuk halaman verifikasi
        session(['otp_user_id' => $user->id]);
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
