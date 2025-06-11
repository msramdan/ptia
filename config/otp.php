<?php

return [
    'is_send_otp'       => env('IS_SEND_OTP', false),
    'expired_otp'       => env('EXPIRED_OTP', 3),

    // Tambahkan semua variabel email di sini
    'mail_mailer'       => env('MAIL_MAILER'),
    'mail_host'         => env('MAIL_HOST'),
    'mail_port'         => env('MAIL_PORT'),
    'mail_username'     => env('MAIL_USERNAME'),
    'mail_password'     => env('MAIL_PASSWORD'),
    'mail_encryption'   => env('MAIL_ENCRYPTION'),
    'mail_from_address' => env('MAIL_FROM_ADDRESS'),
    'mail_from_name'    => env('MAIL_FROM_NAME'),
];
