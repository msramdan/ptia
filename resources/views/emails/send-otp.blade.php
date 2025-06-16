<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi Anda</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            word-spacing: normal;
            background-color: #f4f4f7;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .content-cell {
            padding: 35px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 16px;
            color: #1a202c;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
        }

        p {
            margin-top: 0;
            margin-bottom: 16px;
            color: #4a5568;
            font-size: 16px;
            line-height: 1.625;
            text-align: center;
        }

        .otp-code {
            display: inline-block;
            margin: 20px auto;
            padding: 12px 24px;
            background-color: #edf2f7;
            border-radius: 6px;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 4px;
            color: #1a202c;
        }

        .footer {
            font-size: 12px;
            color: #a0aec0;
            text-align: center;
        }

        .footer a {
            color: #435ebe;
            text-decoration: none;
        }
    </style>
</head>

<body style="margin: 0; padding: 0; word-spacing: normal; background-color: #f4f4f7;">
    @php
        $settingApp = get_setting();
        $appName = $settingApp->nama_aplikasi ?? config('app.name');
        $appUrl = url('/');
    @endphp

    <div style="display: none; max-height: 0; overflow: hidden;">
        Kode verifikasi Anda untuk {{ $appName }} adalah: {{ $otpCode }}
    </div>

    <div role="article" aria-roledescription="email" lang="id"
        style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" class="container"
            style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
            <tr>
                <td class="content-cell" style="padding: 35px;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                        style="width: 100%; border-bottom: 1px solid #e8e8e8; padding-bottom: 25px; margin-bottom: 25px;">
                        <tr>
                            <td align="center">
                                <a href="{{ $appUrl }}" style="text-decoration: none;">
                                    @if ($settingApp?->logo_login)
                                        <img src="{{ url('storage/uploads/logo-logins/' . $settingApp->logo_login) }}"
                                            alt="{{ $appName }} Logo" width="180"
                                            style="max-width: 180px; height: auto; border: 0;">
                                    @else
                                        <h2 style="color: #2d3748; margin: 0;">{{ $appName }}</h2>
                                    @endif
                                </a>
                            </td>
                        </tr>
                    </table>

                    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                        style="width: 100%;">
                        <tr>
                            <td align="center">
                                <h1
                                    style="margin-top: 0; margin-bottom: 16px; color: #1a202c; font-size: 28px; font-weight: 700; text-align: center;">
                                    Kode Verifikasi Anda</h1>
                                <p
                                    style="margin-top: 0; margin-bottom: 16px; color: #4a5568; font-size: 16px; line-height: 1.625; text-align: center;">
                                    Silakan gunakan kode di bawah ini untuk menyelesaikan proses login Anda.</p>

                                <div class="otp-code"
                                    style="display: inline-block; margin: 20px auto; padding: 12px 24px; background-color: #edf2f7; border-radius: 6px; font-size: 32px; font-weight: 700; letter-spacing: 4px; color: #1a202c;">
                                    {{ $otpCode }}
                                </div>

                                <p
                                    style="margin-top: 0; margin-bottom: 16px; color: #4a5568; font-size: 16px; line-height: 1.625; text-align: center;">
                                    Kode ini hanya berlaku untuk <strong>{{ $expireInMinutes }} menit</strong>.
                                    <br>
                                    <small>Jika Anda tidak meminta kode ini, mohon abaikan email ini.</small>
                                </p>
                            </td>
                        </tr>
                    </table>

                    <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                        style="width: 100%; border-top: 1px solid #e8e8e8; padding-top: 25px; margin-top: 25px;">
                        <tr>
                            <td align="center" class="footer"
                                style="font-size: 12px; color: #a0aec0; text-align: center;">
                                &copy; {{ date('Y') }} <a href="{{ $appUrl }}"
                                    style="color: #435ebe; text-decoration: none;">{{ $appName }}</a>. All rights
                                reserved.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
