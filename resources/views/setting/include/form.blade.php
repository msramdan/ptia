<div class="row">
    <!-- Pengaturan Dasar -->
    <div class="col-md-6">
        <div class="form-group">
            <label for="nama-aplikasi">{{ __('Nama Aplikasi') }}</label>
            <input type="text" name="nama_aplikasi" id="nama-aplikasi"
                class="form-control @error('nama_aplikasi') is-invalid @enderror"
                value="{{ isset($setting) ? $setting->nama_aplikasi : old('nama_aplikasi') }}"
                placeholder="{{ __('Nama Aplikasi') }}" required />
            @error('nama_aplikasi')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="tentang-aplikasi">{{ __('Tentang Aplikasi') }}</label>
            <textarea name="tentang_aplikasi" id="tentang-aplikasi"
                class="form-control @error('tentang_aplikasi') is-invalid @enderror"
                placeholder="{{ __('Deskripsi tentang aplikasi') }}" required>{{ isset($setting) ? $setting->tentang_aplikasi : old('tentang_aplikasi') }}</textarea>
            @error('tentang_aplikasi')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <!-- Pengaturan Logo -->
    @isset($setting)
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5 text-center">
                    @if (!$setting->logo)
                        <img src="https://placehold.co/350x200?text=Tidak+Ada+Gambar" alt="Logo"
                            class="rounded mb-2 mt-2 img-fluid">
                    @else
                        <img src="{{ asset('storage/uploads/logos/' . $setting->logo) }}" alt="Logo"
                            class="rounded mb-2 mt-2 img-fluid">
                    @endif
                </div>
                <div class="col-md-7">
                    <div class="form-group ms-3">
                        <label for="logo">{{ __('Logo') }}</label>
                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror"
                            id="logo">
                        @error('logo')
                            <span class="text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                        <div id="logo-help-block" class="form-text">
                            {{ __('Biarkan kosong jika tidak ingin mengubah logo') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-6">
            <div class="form-group">
                <label for="logo">{{ __('Logo') }}</label>
                <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" id="logo"
                    required>
                @error('logo')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    @endisset

    @isset($setting)
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5 text-center">
                    @if (!$setting->logo_login)
                        <img src="https://placehold.co/350x200?text=Tidak+Ada+Gambar" alt="Logo Login"
                            class="rounded mb-2 mt-2 img-fluid">
                    @else
                        <img src="{{ asset('storage/uploads/logo-logins/' . $setting->logo_login) }}" alt="Logo Login"
                            class="rounded mb-2 mt-2 img-fluid">
                    @endif
                </div>
                <div class="col-md-7">
                    <div class="form-group ms-3">
                        <label for="logo_login">{{ __('Logo Login') }}</label>
                        <input type="file" name="logo_login"
                            class="form-control @error('logo_login') is-invalid @enderror" id="logo_login">
                        @error('logo_login')
                            <span class="text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                        <div id="logo_login-help-block" class="form-text">
                            {{ __('Biarkan kosong jika tidak ingin mengubah logo login') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-6">
            <div class="form-group">
                <label for="logo_login">{{ __('Logo Login') }}</label>
                <input type="file" name="logo_login" class="form-control @error('logo_login') is-invalid @enderror"
                    id="logo_login" required>
                @error('logo_login')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    @endisset

    <!-- Pengaturan Favicon -->
    @isset($setting)
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5">
                    @if (!$setting->favicon)
                        <img src="https://placehold.co/350x200?text=Tidak+Ada+Gambar" alt="Favicon"
                            class="rounded mb-2 mt-2 img-fluid">
                    @else
                        <img src="{{ asset('storage/uploads/favicons/' . $setting->favicon) }}" alt="Favicon"
                            class="rounded mb-2 mt-2 img-fluid" style="width: 70px">
                    @endif
                </div>
                <div class="col-md-7">
                    <div class="form-group ms-3">
                        <label for="favicon">{{ __('Favicon') }}</label>
                        <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror"
                            id="favicon">
                        @error('favicon')
                            <span class="text-danger">
                                {{ $message }}
                            </span>
                        @enderror
                        <div id="favicon-help-block" class="form-text">
                            {{ __('Biarkan kosong jika tidak ingin mengubah favicon') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-6">
            <div class="form-group">
                <label for="favicon">{{ __('Favicon') }}</label>
                <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror"
                    id="favicon" required>
                @error('favicon')
                    <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    @endisset

    <!-- Pengaturan Pengumuman -->
    <div class="col-md-6">
        <div class="form-group">
            <label for="is-aktif-pengumuman">{{ __('Aktifkan Pengumuman') }}</label>
            <select class="form-select @error('is_aktif_pengumuman') is-invalid @enderror" name="is_aktif_pengumuman"
                id="is-aktif-pengumuman" required>
                <option value="" selected disabled>-- {{ __('Pilih status pengumuman') }} --</option>
                <option value="Yes"
                    {{ isset($setting) && $setting->is_aktif_pengumuman == 'Yes' ? 'selected' : (old('is_aktif_pengumuman') == 'Yes' ? 'selected' : '') }}>
                    Ya</option>
                <option value="No"
                    {{ isset($setting) && $setting->is_aktif_pengumuman == 'No' ? 'selected' : (old('is_aktif_pengumuman') == 'No' ? 'selected' : '') }}>
                    Tidak</option>
            </select>
            @error('is_aktif_pengumuman')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="pengumuman">{{ __('Pengumuman') }}</label>
            <textarea name="pengumuman" id="pengumuman" class="form-control @error('pengumuman') is-invalid @enderror"
                placeholder="{{ __('Teks pengumuman') }}" required>{{ isset($setting) ? $setting->pengumuman : old('pengumuman') }}</textarea>
            @error('pengumuman')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <!-- Pengaturan Deadline -->
    <div class="col-md-6">
        <div class="form-group">
            <label for="deadline_pengisian">{{ __('Deadline Pengisian (hari)') }}</label>
            <input type="number" name="deadline_pengisian" id="deadline_pengisian"
                class="form-control @error('deadline_pengisian') is-invalid @enderror"
                value="{{ isset($setting) ? $setting->deadline_pengisian : old('deadline_pengisian', 7) }}"
                placeholder="{{ __('Masukkan jumlah hari deadline') }}" min="1" required />
            @error('deadline_pengisian')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
            <div class="form-text">{{ __('Jumlah hari untuk deadline pengisian kuesioner') }}</div>
        </div>
    </div>
</div>

<!-- Pengaturan Cron Job -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Pengaturan Cron Job') }}</h4>
                <p class="card-subtitle">{{ __('Aktifkan atau nonaktifkan fitur cron job otomatis') }}</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Pengaturan Notifikasi -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Notifikasi Alumni') }}</label>
                            <div class="form-check form-switch">
                                <!-- Input hidden untuk nilai default 'No' -->
                                <input type="hidden" name="cron_notif_alumni" value="No">
                                <input type="checkbox" name="cron_notif_alumni" class="form-check-input"
                                    id="cron_notif_alumni" value="Yes"
                                    {{ (isset($setting) && $setting->cron_notif_alumni == 'Yes') || old('cron_notif_alumni', 'No') == 'Yes' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cron_notif_alumni">
                                    {{ __('Aktifkan notifikasi untuk alumni') }}
                                </label>
                            </div>
                            <div class="form-text">
                                {{ __('Sistem akan mengirim notifikasi pengisian kuesioner ke alumni sesuai jadwal cron') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Notifikasi Atasan') }}</label>
                            <div class="form-check form-switch">
                                <!-- Input hidden untuk nilai default 'No' -->
                                <input type="hidden" name="cron_notif_atasan" value="No">
                                <input type="checkbox" name="cron_notif_atasan" class="form-check-input"
                                    id="cron_notif_atasan" value="Yes"
                                    {{ (isset($setting) && $setting->cron_notif_atasan == 'Yes') || old('cron_notif_atasan', 'No') == 'Yes' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cron_notif_atasan">
                                    {{ __('Aktifkan notifikasi untuk atasan') }}
                                </label>
                            </div>
                            <div class="form-text">
                                {{ __('Sistem akan mengirim notifikasi pengisian kuesioner ke atasan sesuai jadwal cron') }}
                            </div>
                        </div>
                    </div>

                    <!-- Pengaturan Hari Cron -->
                    <div class="col-md-6 mt-3">
                        <div class="form-group">
                            <label>{{ __('Hari Eksekusi Cron') }}</label>
                            <div class="d-flex flex-wrap gap-3 mt-2">
                                @php
                                    $days = [
                                        0 => 'Minggu',
                                        1 => 'Senin',
                                        2 => 'Selasa',
                                        3 => 'Rabu',
                                        4 => 'Kamis',
                                        5 => 'Jumat',
                                        6 => 'Sabtu',
                                    ];
                                    $selectedDays = isset($setting) ? $setting->hari_jalan_cron : [];
                                    if (is_string($selectedDays)) {
                                        $selectedDays = json_decode($selectedDays, true);
                                    }
                                    $selectedDays = is_array($selectedDays) ? $selectedDays : [];
                                    $oldDays = old('hari_jalan_cron', []);
                                @endphp

                                @foreach ($days as $value => $day)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="hari_jalan_cron[]"
                                            value="{{ $value }}" id="day-{{ $value }}"
                                            {{ in_array($value, $selectedDays) || in_array($value, $oldDays) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="day-{{ $value }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('hari_jalan_cron')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                            <div class="form-text">
                                {{ __('Pilih hari-hari untuk menjalankan cron job notifikasi Alumni & Atasan') }}
                            </div>
                        </div>
                    </div>

                    <!-- Pengaturan Waktu Cron -->
                    <div class="col-md-3 mt-3">
                        <div class="form-group">
                            <label for="jam_mulai">{{ __('Jam Mulai Cron') }}</label>
                            <input type="time" name="jam_mulai" id="jam_mulai"
                                class="form-control @error('jam_mulai') is-invalid @enderror"
                                value="{{ isset($setting) ? \Carbon\Carbon::parse($setting->jam_mulai)->format('H:i') : old('jam_mulai', '07:00') }}"
                                required />
                            @error('jam_mulai')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3 mt-3">
                        <div class="form-group">
                            <label for="jam_selesai">{{ __('Jam Selesai Cron') }}</label>
                            <input type="time" name="jam_selesai" id="jam_selesai"
                                class="form-control @error('jam_selesai') is-invalid @enderror"
                                value="{{ isset($setting) ? \Carbon\Carbon::parse($setting->jam_selesai)->format('H:i') : old('jam_selesai', '17:00') }}"
                                required />
                            @error('jam_selesai')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <!-- Pengaturan Cron Lainnya -->
                    <div class="col-md-6 mt-3">
                        <div class="form-group">
                            <label>{{ __('Otomatis Insert Kuesioner Atasan Expired') }}</label>
                            <div class="form-check form-switch">
                                <!-- Input hidden untuk nilai default 'No' -->
                                <input type="hidden" name="cron_auto_insert_expired_atasan" value="No">
                                <input type="checkbox" name="cron_auto_insert_expired_atasan"
                                    class="form-check-input" id="cron_auto_insert_expired_atasan" value="Yes"
                                    {{ (isset($setting) && $setting->cron_auto_insert_expired_atasan == 'Yes') || old('cron_auto_insert_expired_atasan', 'No') == 'Yes' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cron_auto_insert_expired_atasan">
                                    {{ __('Otomatis Insert Kuesioner Atasan Expired') }}
                                </label>
                            </div>
                            <div class="form-text">
                                {{ __('Sistem akan otomatis insert data untuk kuesioner atasan dengan meng copy isian alumni') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mt-3">
                        <div class="form-group">
                            <label>{{ __('Otomatis Buat Project') }}</label>
                            <div class="form-check form-switch">
                                <!-- Input hidden untuk nilai default 'No' -->
                                <input type="hidden" name="cron_auto_create_project" value="No">
                                <input type="checkbox" name="cron_auto_create_project" class="form-check-input"
                                    id="cron_auto_create_project" value="Yes"
                                    {{ (isset($setting) && $setting->cron_auto_create_project == 'Yes') || old('cron_auto_create_project', 'No') == 'Yes' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cron_auto_create_project">
                                    {{ __('Otomatis buat project baru') }}
                                </label>
                            </div>
                            <div class="form-text">
                                {{ __('Sistem akan otomatis membuat proyek baru jika tanggal selesai pelaksanaan diklat melebihi 90 hari.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manual Trigger Cron Jobs -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ __('Manual Trigger Cron Jobs') }}</h4>
                <p class="card-subtitle">{{ __('Jalankan cron job secara manual') }}</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success w-100" onclick="triggerCron('notif-alumni')">
                            <i class="fas fa-bell me-2"></i> CRON NOTIF PTIA ALUMNI
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success w-100" onclick="triggerCron('notif-atasan')">
                            <i class="fas fa-bell me-2"></i> CRON NOTIF PTIA ATASAN
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success w-100" onclick="triggerCron('auto-project')">
                            <i class="fas fa-plus-circle me-2"></i> CRON AUTO CREATE PROJECT
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success w-100" onclick="triggerCron('auto-atasan')">
                            <i class="fas fa-sync-alt me-2"></i> CRON INSERT EXPIRED ATASAN
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function triggerCron(type) {
            let path, title, text;

            switch (type) {
                case 'notif-alumni':
                    path = '/kirim-notifikasi-alumni';
                    title = 'Kirim Notifikasi Alumni';
                    text = 'Anda yakin ingin menjalankan CRON NOTIF PTIA ALUMNI?';
                    break;
                case 'notif-atasan':
                    path = '/kirim-notifikasi-atasan';
                    title = 'Kirim Notifikasi Atasan';
                    text = 'Anda yakin ingin menjalankan CRON NOTIF PTIA ATASAN?';
                    break;
                case 'auto-project':
                    path = '/auto-create-project';
                    title = 'Buat Project Otomatis';
                    text = 'Anda yakin ingin menjalankan CRON AUTO CREATE PROJECT?';
                    break;
                case 'auto-atasan':
                    path = '/auto-insert-kuesiober-atasan';
                    title = 'Insert Kuesioner Atasan Expired';
                    text = 'Anda yakin ingin menjalankan CRON INSERT EXPIRED ATASAN?';
                    break;
            }

            const url = '{{ url('') }}' + path;

            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Jalankan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang memproses...',
                        html: 'Silakan tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire(
                                'Berhasil!',
                                data.message || 'Cron job berhasil dijalankan',
                                'success'
                            );
                        })
                        .catch(error => {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menjalankan cron job',
                                'error'
                            );
                            console.error('Error:', error);
                        });
                }
            });
        }
    </script>
@endpush
