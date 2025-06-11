<x-mail::message>
    # Kode Verifikasi Anda

    Gunakan kode di bawah ini untuk menyelesaikan proses login Anda. Jangan bagikan kode ini kepada siapa pun.

    <x-mail::panel>
        ## {{ $otp }}
    </x-mail::panel>

    Kode ini akan kedaluwarsa dalam beberapa menit.

    Jika Anda tidak meminta kode verifikasi, Anda bisa mengabaikan email ini.

    Terima kasih,<br>
    {{ config('app.name') }}
</x-mail::message>
