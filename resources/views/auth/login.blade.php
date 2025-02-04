<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman login - {{ env('APP_NAME') }}</title>
    <link rel="shortcut icon" href="{{ asset('mazer') }}/icon.png" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://registrasi.bpkp.go.id/ptia/assets/temalogin/css/iofrm-style.css">
    <link rel="stylesheet" href="https://registrasi.bpkp.go.id/ptia/assets/temalogin/css/iofrm-theme22.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
</head>
<style>
    .recaptcha-wrapper {
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
    }

    @media (max-width: 576px) {
        .recaptcha-wrapper {
            transform: scale(0.9);
            transform-origin: center;
        }
    }
</style>

<body>
    <div class="form-body without-side">
        <div class="row">
            <div class="col-md-6 img-holder">
                <div class="bg"
                    style="background: rgb(235, 245, 255);
                           background: radial-gradient(circle, rgb(60, 130, 200) 0%, rgb(40, 90, 150) 100%);">
                </div>
                <div
                    style="width:100%;height:100%;z-index:0;top:0;left:0;position:fixed;background:url('https://registrasi.bpkp.go.id/ptia/assets/temalogin/images/pattern-3.svg') no-repeat center bottom fixed;background-size: cover;">
                </div>
            </div>

            <div class="col-md-6 form-holder">
                <div class="form-content">
                    <div class="form-items p-4" style="border-radius:10px;">
                        <div class="text-center">
                            <img src="https://registrasi.bpkp.go.id/ptia/assets/logo/logo_ptia_login.png"
                                class="img-fluid" alt="Logo PTIA">
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

                            <div class="mb-3 text-center">
                                <div class="recaptcha-wrapper">
                                    {!! NoCaptcha::display() !!}
                                </div>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://registrasi.bpkp.go.id/ptia/assets/temalogin/js/jquery.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- SweetAlert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- Custom Scripts -->
    <script>
        $(document).ready(function() {
            // Show error notifications if there are any
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

            // Toggle password visibility
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
