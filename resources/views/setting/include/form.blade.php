<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="nama-aplikasi">{{ __('Nama Aplikasi') }}</label>
            <input type="text" name="nama_aplikasi" id="nama-aplikasi" class="form-control @error('nama_aplikasi') is-invalid @enderror" value="{{ isset($setting) ? $setting->nama_aplikasi : old('nama_aplikasi') }}" placeholder="{{ __('Nama Aplikasi') }}" required />
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
            <textarea name="tentang_aplikasi" id="tentang-aplikasi" class="form-control @error('tentang_aplikasi') is-invalid @enderror" placeholder="{{ __('Tentang Aplikasi') }}" required>{{ isset($setting) ? $setting->tentang_aplikasi : old('tentang_aplikasi') }}</textarea>
            @error('tentang_aplikasi')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    @isset($setting)
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5 text-center">
                    @if (!$setting->logo)
                        <img src="https://placehold.co/350x200?text=No+Image+Available" alt="Logo" class="rounded mb-2 mt-2 img-fluid">
                    @else
                        <img src="{{ asset('storage/uploads/logos/' . $setting->logo) }}" alt="Logo" class="rounded mb-2 mt-2 img-fluid">
                    @endif
                </div>

                <div class="col-md-7">
                    <div class="form-group ms-3">
                        <label for="logo">{{ __('Logo') }}</label>
                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" id="logo">

                        @error('logo')
                          <span class="text-danger">
                                {{ $message }}
                           </span>
                        @enderror
                        <div id="logo-help-block" class="form-text">
                            {{ __('Leave the logo blank if you don`t want to change it.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-6">
            <div class="form-group">
                <label for="logo">{{ __('Logo') }}</label>
                <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" id="logo" required>

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
                        <img src="https://placehold.co/350x200?text=No+Image+Available" alt="Logo Login" class="rounded mb-2 mt-2 img-fluid">
                    @else
                        <img src="{{ asset('storage/uploads/logo-logins/' . $setting->logo_login) }}" alt="Logo Login" class="rounded mb-2 mt-2 img-fluid">
                    @endif
                </div>

                <div class="col-md-7">
                    <div class="form-group ms-3">
                        <label for="logo_login">{{ __('Logo Login') }}</label>
                        <input type="file" name="logo_login" class="form-control @error('logo_login') is-invalid @enderror" id="logo_login">

                        @error('logo_login')
                          <span class="text-danger">
                                {{ $message }}
                           </span>
                        @enderror
                        <div id="logo_login-help-block" class="form-text">
                            {{ __('Leave the logo login blank if you don`t want to change it.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-6">
            <div class="form-group">
                <label for="logo_login">{{ __('Logo Login') }}</label>
                <input type="file" name="logo_login" class="form-control @error('logo_login') is-invalid @enderror" id="logo_login" required>

                @error('logo_login')
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
                <div class="col-md-5">
                    @if (!$setting->favicon)
                        <img src="https://placehold.co/350x200?text=No+Image+Available" alt="Favicon" class="rounded mb-2 mt-2 img-fluid">
                    @else
                        <img src="{{ asset('storage/uploads/favicons/' . $setting->favicon) }}" alt="Favicon" class="rounded mb-2 mt-2 img-fluid" style="width: 70px">
                    @endif
                </div>

                <div class="col-md-7">
                    <div class="form-group ms-3">
                        <label for="favicon">{{ __('Favicon') }}</label>
                        <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror" id="favicon">

                        @error('favicon')
                          <span class="text-danger">
                                {{ $message }}
                           </span>
                        @enderror
                        <div id="favicon-help-block" class="form-text">
                            {{ __('Leave the favicon blank if you don`t want to change it.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-6">
            <div class="form-group">
                <label for="favicon">{{ __('Favicon') }}</label>
                <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror" id="favicon" required>

                @error('favicon')
                   <span class="text-danger">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    @endisset
    <div class="col-md-6">
        <div class="form-group">
            <label for="pengumuman">{{ __('Pengumuman') }}</label>
            <textarea name="pengumuman" id="pengumuman" class="form-control @error('pengumuman') is-invalid @enderror" placeholder="{{ __('Pengumuman') }}" required>{{ isset($setting) ? $setting->pengumuman : old('pengumuman') }}</textarea>
            @error('pengumuman')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="is-aktif-pengumuman">{{ __('Is Aktif Pengumuman') }}</label>
            <select class="form-select @error('is_aktif_pengumuman') is-invalid @enderror" name="is_aktif_pengumuman" id="is-aktif-pengumuman" class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select is aktif pengumuman') }} --</option>
                <option value="Yes" {{ isset($setting) && $setting->is_aktif_pengumuman == 'Yes' ? 'selected' : (old('is_aktif_pengumuman') == 'Yes' ? 'selected' : '') }}>Yes</option>
		<option value="No" {{ isset($setting) && $setting->is_aktif_pengumuman == 'No' ? 'selected' : (old('is_aktif_pengumuman') == 'No' ? 'selected' : '') }}>No</option>
            </select>
            @error('is_aktif_pengumuman')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>
