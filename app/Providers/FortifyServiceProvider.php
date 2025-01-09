<?php

namespace App\Providers;

use App\Actions\Fortify\{CreateNewUser, ResetUserPassword, UpdateUserPassword, UpdateUserProfileInformation};
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ]);

            $response = Http::post(config('stara.endpoint') . '/auth/login', [
                'username' => $request->username,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user = User::where('user_nip', $data['data']['user_info']['user_nip'])->first();

                if (!$user) {
                    $user = createUser($data['data']['user_info']);
                }

                Auth::login($user, $request->filled('remember'));
                session(['api_token' => $data['data']['token']]);
                return $user;
            } else {
                $errorResponse = $response->json();

                if ($errorResponse['status'] === 'Error' && isset($errorResponse['error'])) {
                    throw ValidationException::withMessages([
                        Fortify::username() => $errorResponse['error'],
                    ]);
                }
            }
            throw ValidationException::withMessages([Fortify::username() => [trans('auth.failed')]]);
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', fn(Request $request)  => Limit::perMinute(5)->by($request->session()->get('login.id')));

        Fortify::registerView(fn() => view('auth.register'));

        Fortify::loginView(fn() => view('auth.login'));

        Fortify::confirmPasswordView(fn() => view('auth.confirm-password'));

        Fortify::twoFactorChallengeView(fn() => view('auth.two-factor-challenge'));

        Fortify::requestPasswordResetLinkView(fn() => view('auth.forgot-password'));

        Fortify::resetPasswordView(fn(Request $request) => view('auth.reset-password', ['request' => $request]));


        function createUser($userInfo)
        {
            try {
                $nip_lama = $userInfo['user_nip'];
                $response = Http::get(config('stara.map_endpoint') . '/v2/pegawai/sima/atlas', [
                    'api_token' => config('stara.map_api_token_employee'),
                    's_nip' => $nip_lama
                ]);

                if ($response->successful()) {
                    $unitKerjaData = $response->json();
                    $kode_eselon2 = $unitKerjaData['result'][0]['kode_eselon2'];
                } else {
                    throw new \Exception('Request to endpoint unit kerja failed.');
                }

                $user = User::create([
                    'user_nip' => $userInfo['user_nip'],
                    'name' => $userInfo['name'],
                    'phone' => $userInfo['nomor_hp'] ?? '',
                    'email' => $userInfo['email'],
                    'jabatan' => $userInfo['jabatan'],
                    'kode_unit' => $kode_eselon2,
                    'nama_unit' => $userInfo['namaunit'],
                ]);
                assignRole($user, 1);
                return $user;
            } catch (\Exception $e) {
                report($e);
                abort(500, 'Error creating user: ' . $e->getMessage());
            }
        }

        function assignRole($user, $roleId)
        {
            $role = Role::find($roleId);
            if ($role) {
                $user->assignRole($role);
            } else {
                abort(500, 'Role not found');
            }
        }
    }
}
