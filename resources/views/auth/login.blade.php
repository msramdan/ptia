<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPKP PTIA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://registrasi.bpkp.go.id/ptia/assets/temalogin/css/iofrm-style.css">
    <link rel="stylesheet" href="https://registrasi.bpkp.go.id/ptia/assets/temalogin/css/iofrm-theme22.css">
</head>

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

                        {{-- @if ($errors->any())
                            <div class="alert alert-danger text-center" role="alert">
                                <ul class="list-unstyled mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li><strong>{{ $error }}</strong></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif --}}

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

                            <div class="mb-3">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://registrasi.bpkp.go.id/ptia/assets/temalogin/js/jquery.min.js"></script>
    <script src="https://registrasi.bpkp.go.id/ptia/assets/temaalus/dist/js/m_login3.js"></script>
    <script src="https://registrasi.bpkp.go.id/ptia/assets/temalogin/js/main.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
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
