<x-mail::message>
    # Kode Verifikasi Anda

    Gunakan kode di bawah ini untuk menyelesaikan proses login Anda. Jangan bagikan kode ini kepada siapa pun.

    <x-mail::panel>
        ## {{ $otp }}
    </x-mail::panel>

    Kode ini akan kedaluwarsa dalam **{{ (int) config('otp.expired_otp', 3) }} menit**.

    Jika Anda tidak merasa meminta kode ini, Anda bisa mengabaikan email ini dengan aman.

    Terima kasih,<br>
    {{ config('app.name') }}
</x-mail::message>
