<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="level">{{ __('Level') }}</label>
            <select class="form-select @error('level') is-invalid @enderror" name="level" id="level"
                class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select level') }} --</option>
                <option value="3"
                    {{ isset($aspek) && $aspek->level == '3' ? 'selected' : (old('level') == '3' ? 'selected' : '') }}>3
                </option>
                <option value="4"
                    {{ isset($aspek) && $aspek->level == '4' ? 'selected' : (old('level') == '4' ? 'selected' : '') }}>4
                </option>
            </select>
            @error('level')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="aspek">{{ __('Aspek') }}</label>
            <input type="text" name="aspek" id="aspek"
                class="form-control @error('aspek') is-invalid @enderror"
                value="{{ isset($aspek) ? $aspek->aspek : old('aspek') }}" placeholder="{{ __('Aspek') }}" required />
            @error('aspek')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="kriteria">{{ __('kriteria') }}</label>
            <select class="form-select @error('kriteria') is-invalid @enderror" name="kriteria" id="kriteria"
                class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select kriteria') }} --</option>
                <option value="Skor Persepsi"
                    {{ isset($aspek) && $aspek->kriteria == 'Skor Persepsi' ? 'selected' : (old('kriteria') == 'Skor Persepsi' ? 'selected' : '') }}>
                    Skor Persepsi</option>
                <option value="Delta Skor Persepsi"
                    {{ isset($aspek) && $aspek->kriteria == 'Delta Skor Persepsi' ? 'selected' : (old('kriteria') == 'Delta Skor Persepsi' ? 'selected' : '') }}>
                    Delta Skor Persepsi</option>
            </select>
            @error('kriteria')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="urutan">{{ __('Urutan') }}</label>
            <input type="number" name="urutan" id="urutan"
                class="form-control @error('urutan') is-invalid @enderror"
                value="{{ isset($aspek) ? $aspek->urutan : old('urutan') }}" placeholder="{{ __('Urutan') }}"
                required />
            @error('urutan')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>
