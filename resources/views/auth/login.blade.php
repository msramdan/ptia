<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman login - {{ env('APP_NAME') }}</title>
    @php
        $settingApp = get_setting();
    @endphp

    @if ($settingApp?->favicon)
        <link rel="shortcut icon" href="{{ asset('storage/uploads/favicons/' . $settingApp->favicon) }}"
            type="image/x-icon">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/temalogin/css/iofrm-style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/temalogin/css/iofrm-theme22.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/toastr/css/toastr.min.css') }}">
</head>
<style>
    .captcha-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
        overflow: hidden;
    }

    .captcha-wrapper>div {
        transform: scale(0.87);
        transform-origin: center;
    }

    @media (max-width: 576px) {
        .captcha-wrapper>div {
            transform: scale(0.80);
        }
    }
</style>



<body>
    <div class="form-body without-side">
        <div class="row">
            <div class="col-md-6 img-holder">
                <div class="bg"
                    style="background: rgb(235, 245, 255); background: radial-gradient(circle, rgb(60, 130, 200) 0%, rgb(40, 90, 150) 100%);">
                </div>
                <div
                    style="width:100%;height:100%;z-index:0;top:0;left:0;position:fixed;background:url('{{ asset('assets/temalogin/images/pattern-3.svg') }}') no-repeat center bottom fixed;background-size: cover;">
                </div>
            </div>

            <div class="col-md-6 form-holder">
                <div class="form-content">
                    <div class="form-items p-4" style="border-radius:10px;">
                        <div class="text-center">
                            @if ($settingApp?->logo_login)
                                <img src="{{ asset('storage/uploads/logo-logins/' . $settingApp->logo_login) }}"
                                    class="img-fluid" alt="{{ $settingApp->nama_aplikasi }}">
                            @endif
                        </div>
                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}" id="form" class="mt-3">
                            @csrf
                            <div class="mb-3">
                                <input type="text" class="form-control" id="username" placeholder="Username"
                                    name="username" required autofocus>
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="password" class="form-control" id="password" placeholder="Password"
                                    name="password" required autocomplete="current-password">
                                <i class="fa fa-eye position-absolute toggle-password"
                                    style="top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
                            </div>
                            <div class="mb-3 captcha-wrapper">
                                {!! NoCaptcha::display() !!}
                                {!! NoCaptcha::renderJs() !!}
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        <div class="text-center text-muted mt-4" style="font-size:12px;">
                            © Copyright 2021 Pusdiklatwas. All Rights Reserved.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/temalogin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/sweetalert/js/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}", "Error", {
                        closeButton: true,
                        progressBar: true,
                        positionClass: "toast-top-right",
                        timeOut: 5000,
                    });
                @endforeach
            @endif

            $('.toggle-password').on('click', function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });
        });
    </script>
</body>

</html>
