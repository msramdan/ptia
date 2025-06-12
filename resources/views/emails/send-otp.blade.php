<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi OTP</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background-color: #edf2f7;
            color: #718096;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 570px;
            margin: 0 auto;
            padding: 35px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
        }

        .header img {
            max-height: 50px;
        }

        .content {
            padding: 25px 0;
            text-align: center;
        }

        .content h1 {
            font-size: 22px;
            font-weight: bold;
            color: #2d3748;
            margin-top: 0;
        }

        .content p {
            font-size: 16px;
            line-height: 1.5;
        }

        .otp-code {
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 10px;
            color: #2d3748;
            background-color: #edf2f7;
            padding: 12px 0;
            border-radius: 4px;
            margin: 30px 0;
            display: inline-block;
        }

        .footer {
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #a0aec0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>
        <div class="content">
            <h1>Kode Verifikasi Anda</h1>
            <p>Gunakan kode di bawah ini untuk menyelesaikan proses login.</p>
            <div class="otp-code">
                {{ $otpCode }}
            </div>
            <p>Kode ini hanya berlaku untuk <strong>{{ $expireInMinutes }} menit</strong>. Demi keamanan, jangan berikan
                kode ini kepada siapa pun.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>
