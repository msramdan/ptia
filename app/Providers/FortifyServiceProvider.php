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

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ]);


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

                    if (config('otp.is_send_otp') === true) {
                        // 1. Buat kode OTP
                        $otp = rand(100000, 999999);

                        // 2. Simpan OTP dan WAKTU KEDALUWARSA SEBAGAI STRING
                        // Ini adalah cara yang paling sederhana dan anti-gagal
                        $user->otp_code = $otp;
                        $user->otp_expires_at = date('Y-m-d H:i:s', time() + ((int)config('otp.expired_otp', 3) * 60));
                        $user->save();

                        // 3. Kirim email OTP
                        try {
                            Mail::to($user->email)->send(new SendOtpMail((string)$otp));
                        } catch (\Exception $e) {
                            throw ValidationException::withMessages([
                                'username' => 'Gagal mengirim email. Detail Error: ' . $e->getMessage(),
                            ]);
                        }

                        // 4. Redirect ke halaman verifikasi OTP
                        throw ValidationException::withMessages([
                            'otp_required' => 'OTP diperlukan.',
                        ])->redirectTo(route('otp.show', ['user_id' => $user->id]));
                    }

                    Auth::login($user, $request->filled('remember'));
                    session(['api_token' => $data['data']['token']]);

                    // Cek status pengumuman
                    $setting = Setting::first();
                    if ($setting && $setting->is_aktif_pengumuman === 'Yes') {
                        session(['show_pengumuman' => true]);
                    }
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
