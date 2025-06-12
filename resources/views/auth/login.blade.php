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

            <div class="modal fade" id="otp-modal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="otpModalLabel">Verifikasi Kode OTP</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="otp-error-alert" class="alert alert-danger d-none"></div>
                            <p>Kami telah mengirimkan kode OTP ke email Anda. Silakan masukkan di bawah ini.</p>
                            <form id="otp-form" action="{{ route('login.verify_otp') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" id="otp-user-id">
                                <div class="mb-3">
                                    <label for="otp-input" class="form-label">Kode OTP</label>
                                    <input type="text" class="form-control" id="otp-input" name="otp" required
                                        maxlength="6">
                                </div>
                                <button type="submit" class="btn btn-primary">Verifikasi</button>
                            </form>
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



    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/temalogin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/toastr/js/toastr.min.js') }}"></script>
    {{-- Tambahkan JS SweetAlert dari template Mazer --}}
    <script src="{{ asset('mazer/extensions/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // 1. Tangani pengiriman form login
            $('#login-form').on('submit', function(e) {
                e.preventDefault(); // Mencegah form dikirim secara normal

                var form = $(this);
                var url = form.attr('action');
                var submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Loading...');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Simpan user_id dan tampilkan modal
                            $('#otp-user-id').val(response.user_id);
                            var otpModal = new bootstrap.Modal(document.getElementById(
                                'otp-modal'));
                            otpModal.show();
                        }
                    },
                    error: function(xhr) {
                        // Tampilkan error jika login gagal
                        var error = xhr.responseJSON.message || 'Terjadi kesalahan.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: error,
                            confirmButtonColor: '#435ebe' // Sesuaikan dengan warna tema Anda
                        });
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).text('Log in');
                    }
                });
            });

            // 2. Tangani pengiriman form OTP dari modal
            $('#otp-form').on('submit', function(e) {
                e.preventDefault(); // Mencegah form dikirim secara normal

                var form = $(this);
                var url = form.attr('action');
                var submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Memverifikasi...');

                $('#otp-error-alert').addClass('d-none'); // Sembunyikan alert error

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // 1. Dapatkan instance modal OTP yang sedang aktif
                            var otpModalElement = document.getElementById('otp-modal');
                            var otpModal = bootstrap.Modal.getInstance(otpModalElement);

                            // 2. Tutup modal OTP
                            otpModal.hide();

                            // 3. Tampilkan notifikasi sukses dengan SweetAlert2
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Berhasil!',
                                text: 'Anda akan diarahkan ke dashboard dalam beberapa saat.',
                                timer: 2000, // Notifikasi akan hilang setelah 2 detik
                                showConfirmButton: false, // Sembunyikan tombol "OK"
                                allowOutsideClick: false // Mencegah user menutup notifikasi
                            }).then(() => {
                                // 4. Arahkan ke dashboard setelah notifikasi ditutup
                                window.location.href = response.redirect_url;
                            });
                        }
                    },
                    error: function(xhr) {
                        // Tampilkan error di dalam modal
                        var error = xhr.responseJSON.message || 'Terjadi kesalahan.';

                        $('#otp-error-alert').text(error).removeClass('d-none');
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
