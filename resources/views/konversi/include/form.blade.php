<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="jenis-skor">{{ __('Jenis Skor') }}</label>
            <input type="text" name="jenis_skor" id="jenis-skor" class="form-control @error('jenis_skor') is-invalid @enderror" value="{{ isset($konversi) ? $konversi->jenis_skor : old('jenis_skor') }}" placeholder="{{ __('Jenis Skor') }}" required />
            @error('jenis_skor')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="skor">{{ __('Skor') }}</label>
            <input type="number" name="skor" id="skor" class="form-control @error('skor') is-invalid @enderror" value="{{ isset($konversi) ? $konversi->skor : old('skor') }}" placeholder="{{ __('Skor') }}" required />
            @error('skor')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="konversi">{{ __('Konversi') }}</label>
            <input type="number" name="konversi" id="konversi" class="form-control @error('konversi') is-invalid @enderror" value="{{ isset($konversi) ? $konversi->konversi : old('konversi') }}" placeholder="{{ __('Konversi') }}" required />
            @error('konversi')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>