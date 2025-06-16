<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi OTP</title>
    <style>
        /* Gaya dasar yang ramah untuk email */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background-color: #f7fafc;
            color: #718096;
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 35px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .header {
            text-align: center;
            padding-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
        }

        .header img {
            max-height: 45px;
            /* Sedikit disesuaikan agar tidak terlalu besar */
            width: auto;
        }

        .content {
            padding: 30px 0;
            text-align: center;
        }

        .content h1 {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .otp-code {
            font-size: 38px;
            font-weight: 700;
            letter-spacing: 8px;
            /* Sedikit dikurangi untuk estetika */
            color: #2d3748;
            background-color: #edf2f7;
            padding: 15px 25px;
            border-radius: 6px;
            margin: 20px auto 30px;
            display: inline-block;
        }

        .footer {
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #a0aec0;
        }

        .footer a {
            color: #435ebe;
            text-decoration: none;
        }
    </style>
</head>

<body>
    @php
        // Mengambil data setting aplikasi menggunakan helper yang sudah ada
        $settingApp = get_setting();
    @endphp
    <div class="container">
        <div class="header">
            <a href="{{ url('/') }}" style="text-decoration: none;">
                @if ($settingApp?->logo_login)
                    {{-- Menampilkan logo jika ada di pengaturan --}}
                    <img src="{{ asset('storage/uploads/logo-logins/' . $settingApp->logo_login) }}"
                        alt="{{ $settingApp->nama_aplikasi ?? 'Logo' }}">
                @else
                    {{-- Fallback jika logo tidak ada, tampilkan nama aplikasi --}}
                    <h2 style="color: #2d3748; margin: 0;">{{ $settingApp->nama_aplikasi ?? config('app.name') }}</h2>
                @endif
            </a>
        </div>
        <div class="content">
            <h1>Kode Verifikasi Anda</h1>
            <p>Gunakan kode di bawah ini untuk menyelesaikan proses login. Kode ini hanya berlaku untuk
                <strong>{{ $expireInMinutes }} menit</strong>.</p>
            <div class="otp-code">
                {{ $otpCode }}
            </div>
            <p>Demi keamanan, jangan pernah memberikan kode ini kepada siapa pun, termasuk staf kami.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} <a
                href="{{ url('/') }}">{{ $settingApp->nama_aplikasi ?? config('app.name') }}</a>. All rights
            reserved.
        </div>
    </div>
</body>

</html>
