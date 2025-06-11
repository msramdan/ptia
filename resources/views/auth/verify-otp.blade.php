@extends('layouts.auth')

@section('title', 'Verifikasi OTP')

@section('content')
    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0">
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    <img src="{{ asset('mazer/compiled/svg/auth-v2-login-illustration-light.svg') }}" class="img-fluid"
                        alt="Login V2" data-app-light-img="illustrations/auth-v2-login-illustration-light.svg"
                        data-app-dark-img="illustrations/auth-v2-login-illustration-dark.svg" />
                </div>
            </div>

            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title fw-bold mb-1">Verifikasi Dua Langkah 💬</h2>
                    <p class="card-text mb-2">
                        Kami telah mengirimkan kode verifikasi ke email Anda. Masukkan kode tersebut untuk melanjutkan.
                    </p>

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="auth-login-form mt-2" action="{{ route('otp.verify') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ request()->query('user_id') }}">

                        <div class="mb-1">
                            <label for="otp" class="form-label">Masukkan Kode OTP 6 Digit</label>
                            <input type="text" class="form-control" id="otp" name="otp"
                                placeholder="Masukkan kode OTP" autofocus maxlength="6" />
                        </div>
                        <button class="btn btn-primary w-100" tabindex="4">Verifikasi Akun Saya</button>
                    </form>

                    <p class="text-center mt-2">
                        <span>Tidak menerima kode?</span>
                        <a href="#"> <span>&nbsp;Kirim ulang</span> </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
