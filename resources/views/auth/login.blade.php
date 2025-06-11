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
    {{-- Tambahkan CSS SweetAlert dari template Mazer --}}
    <link rel="stylesheet" href="{{ asset('mazer/extensions/sweetalert2/sweetalert2.min.css') }}">
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
</head>

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
                        <form method="POST" action="{{ route('login') }}" id="login-form" class="mt-3">
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
    {{-- Tambahkan JS SweetAlert dari template Mazer --}}
    <script src="{{ asset('mazer/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}", "Error");
                @endforeach
            @endif

            $('.toggle-password').on('click', function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            // LOGIKA AJAX BARU
            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const submitButton = form.find('button[type="submit"]');
                const originalButtonText = submitButton.html();
                submitButton.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                    ).prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(response) {
                        if (response.otp_required) {
                            submitButton.html(originalButtonText).prop('disabled', false);
                            showOtpModal(response.user_id, response.email);
                        } else {
                            window.location.href = "{{ config('fortify.home') }}";
                        }
                    },
                    error: function(xhr) {
                        submitButton.html(originalButtonText).prop('disabled', false);
                        const errors = xhr.responseJSON.errors || {
                            general: [xhr.responseJSON.message ||
                                'Terjadi kesalahan tidak diketahui.'
                            ]
                        };
                        for (const key in errors) {
                            toastr.error(errors[key][0]);
                        }
                        grecaptcha.reset();
                    }
                });
            });

            function showOtpModal(userId, userEmail) {
                Swal.fire({
                    title: 'Verifikasi Dua Langkah',
                    html: `<p class="text-center text-muted fs-6">Kami telah mengirimkan kode 6 digit ke email Anda:<br><b>${userEmail}</b></p>`,
                    input: 'text',
                    inputPlaceholder: 'Masukkan kode OTP',
                    inputAttributes: {
                        autocapitalize: 'off',
                        maxlength: 6,
                        inputmode: 'numeric',
                        pattern: '[0-9]*'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Verifikasi',
                    cancelButtonText: 'Batal',
                    footer: '<a href="#" id="resend-otp-link">Tidak menerima kode? Kirim ulang</a>',
                    showLoaderOnConfirm: true,
                    preConfirm: (otp) => {
                        if (!otp || !/^\d{6}$/.test(otp)) {
                            Swal.showValidationMessage('Harap masukkan 6 digit kode OTP');
                            return false;
                        }
                        return $.ajax({
                            type: "POST",
                            url: "{{ route('otp.verify.modal') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                user_id: userId,
                                otp: otp
                            }
                        }).catch(xhr => Swal.showValidationMessage(
                            `Gagal: ${xhr.responseJSON.message}`));
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Login berhasil, mengarahkan ke dashboard.',
                            icon: 'success'
                        });
                        window.location.href = result.value.redirect_url;
                    }
                });
            }

            // Event listener untuk link "kirim ulang" di dalam modal
            $(document).on('click', '#resend-otp-link', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "{{ route('otp.resend.modal') }}",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        toastr.success(response.message);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
</body>

</html>
