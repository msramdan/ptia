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

        /* --- CSS Baru untuk Modal OTP --- */
        #otp-modal .modal-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .otp-icon {
            font-size: 3rem;
            color: #435ebe;
            margin-bottom: 1rem;
        }

        #otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .otp-input {
            width: 45px;
            height: 50px;
            font-size: 1.5rem;
            text-align: center;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .otp-input:focus {
            border-color: #435ebe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(67, 94, 190, 0.25);
        }

        #otp-form button {
            width: 100%;
        }

        .resend-otp-container {
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        #resend-otp-link {
            color: #435ebe;
            text-decoration: none;
            cursor: pointer;
        }

        #resend-otp-link.disabled {
            color: #6c757d;
            cursor: not-allowed;
            pointer-events: none;
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

            <div class="modal fade" id="otp-modal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-3">
                        <div class="modal-header border-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <i class="fa fa-envelope-open-text otp-icon"></i>
                            <h5 class="modal-title" id="otpModalLabel">Verifikasi Email Anda</h5>
                            <p class="text-muted mt-2">
                                Kami telah mengirimkan kode verifikasi 6 digit ke email Anda.
                            </p>

                            <form id="otp-form" action="{{ route('login.verify_otp') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" id="otp-user-id">
                                <input type="hidden" name="otp" id="otp-combined">

                                <div id="otp-inputs">
                                    <input type="text" class="form-control otp-input" maxlength="1">
                                    <input type="text" class="form-control otp-input" maxlength="1">
                                    <input type="text" class="form-control otp-input" maxlength="1">
                                    <input type="text" class="form-control otp-input" maxlength="1">
                                    <input type="text" class="form-control otp-input" maxlength="1">
                                    <input type="text" class="form-control otp-input" maxlength="1">
                                </div>
                                <div id="otp-error-alert" class="alert alert-danger d-none p-2 text-center"
                                    style="font-size: 0.85rem;"></div>

                                <button type="submit" class="btn btn-primary">Verifikasi</button>
                            </form>

                            <div class="resend-otp-container">
                                Tidak menerima kode?
                                <a href="#" id="resend-otp-link">Kirim Ulang</a>
                                <span id="otp-timer" class="d-none"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 form-holder">
                <div class="form-content">
                    <div class="form-items p-4" style="border-radius:10px;">
                        <div class="text-center">
                            @if ($settingApp?->logo_login)
                                <img src="{{ asset('storage/uploads/logo-logins/' . $settingApp->logo_login) }}"
                                    class="img-fluid" alt="{{ $settingApp->nama_aplikasi }}">
                            @else
                                {{-- Fallback jika logo tidak ada, tampilkan nama aplikasi --}}
                                <h2 style="color: #2d3748; margin: 0;">
                                    {{ $settingApp->nama_aplikasi ?? config('app.name') }}</h2>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('login.send_otp') }}" id="login-form" class="mt-3">
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



    <script src="{{ asset('assets/jquery/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('mazer/extensions/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).on('click', '.toggle-password', function() {
            var input = $('#password');
            var icon = $(this);
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            let otpModalInstance;

            // Inisialisasi modal saat dokumen siap
            const otpModalElement = document.getElementById('otp-modal');
            if (otpModalElement) {
                otpModalInstance = new bootstrap.Modal(otpModalElement);
            }

            // --- Logika untuk Kirim OTP (Form Login) ---
            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#otp-user-id').val(response.user_id);
                            if (otpModalInstance) {
                                otpModalInstance.show();
                            }
                        }
                    },
                    error: function(xhr) {
                        var error = xhr.responseJSON.message || 'Terjadi kesalahan.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: error,
                            confirmButtonColor: '#435ebe'
                        });
                        // Reset captcha jika ada
                        if (typeof grecaptcha !== 'undefined') {
                            grecaptcha.reset();
                        }
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).text('Login');
                    }
                });
            });

            // --- Logika untuk Modal OTP ---
            const otpInputs = document.querySelectorAll('.otp-input');
            const otpForm = document.getElementById('otp-form');
            const combinedOtpInput = document.getElementById('otp-combined');
            const otpErrorAlert = $('#otp-error-alert');

            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    // Hanya izinkan angka
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                    if (input.value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === "Backspace" && !input.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').slice(0, 6);
                    if (/^\d{6}$/.test(pastedData)) {
                        otpInputs.forEach((box, i) => {
                            box.value = pastedData[i] || '';
                        });
                        otpInputs[otpInputs.length - 1].focus();
                    }
                });
            });

            otpModalElement.addEventListener('shown.bs.modal', function() {
                otpInputs[0].focus();
                // startOtpTimer(); // Anda bisa memanggil timer di sini jika diperlukan
            });

            otpModalElement.addEventListener('hidden.bs.modal', function() {
                // Reset form saat modal ditutup
                otpForm.reset();
                otpInputs.forEach(input => input.value = '');
                otpErrorAlert.addClass('d-none');
            });


            // --- Logika untuk Verifikasi OTP ---
            $('#otp-form').on('submit', function(e) {
                e.preventDefault();
                otpErrorAlert.addClass('d-none');

                // Gabungkan nilai dari semua input OTP
                let combinedOtp = '';
                otpInputs.forEach(input => {
                    combinedOtp += input.value;
                });
                combinedOtpInput.value = combinedOtp;

                // Cek jika OTP tidak lengkap
                if (combinedOtp.length !== 6) {
                    otpErrorAlert.text('Harap isi 6 digit kode OTP.').removeClass('d-none');
                    return;
                }

                var form = $(this);
                var url = form.attr('action');
                var submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memverifikasi...'
                );


                $.ajax({
                    type: 'POST',
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            if (otpModalInstance) {
                                otpModalInstance.hide();
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Berhasil!',
                                text: 'Anda akan diarahkan ke dashboard.',
                                timer: 2000,
                                showConfirmButton: false,
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = response.redirect_url;
                            });
                        }
                    },
                    error: function(xhr) {
                        var error = xhr.responseJSON.message || 'Terjadi kesalahan.';
                        otpErrorAlert.text(error).removeClass('d-none');
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).text('Verifikasi');
                    }
                });
            });
        });
    </script>

</body>

</html>
