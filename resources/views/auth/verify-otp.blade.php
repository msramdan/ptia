<form class="auth-login-form mt-2" action="{{ route('otp.verify') }}" method="POST">
    @csrf
    <div class="mb-1">
        <label for="otp" class="form-label">Masukkan Kode OTP 6 Digit</label>
        <input type="text" class="form-control" id="otp" name="otp" placeholder="Masukkan kode OTP" autofocus
            maxlength="6" />
    </div>
    <button class="btn btn-primary w-100" tabindex="4">Verifikasi Akun Saya</button>
</form>
