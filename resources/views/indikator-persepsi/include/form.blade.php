<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="aspek-id">{{ __('Aspek') }}</label>
            <select class="form-select @error('aspek_id') is-invalid @enderror" name="aspek_id" id="aspek-id"
                class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select aspek') }} --</option>

                @foreach ($aspeks as $aspek)
                    <option value="{{ $aspek?->id }}"
                        {{ isset($indikatorPersepsi) && $indikatorPersepsi?->aspek_id == $aspek?->id ? 'selected' : (old('aspek_id') == $aspek?->id ? 'selected' : '') }}>
                        {{ $aspek?->level }}
                    </option>
                @endforeach
            </select>
            @error('aspek_id')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="indikator-persepsi">{{ __('Indikator Persepsi') }}</label>
            <select class="form-select @error('indikator_persepsi') is-invalid @enderror" name="indikator_persepsi"
                id="indikator-persepsi" class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select indikator persepsi') }} --</option>
                <option value="1"
                    {{ isset($indikatorPersepsi) && $indikatorPersepsi->indikator_persepsi == '1' ? 'selected' : (old('indikator_persepsi') == '1' ? 'selected' : '') }}>
                    1</option>
                <option value="2"
                    {{ isset($indikatorPersepsi) && $indikatorPersepsi->indikator_persepsi == '2' ? 'selected' : (old('indikator_persepsi') == '2' ? 'selected' : '') }}>
                    2</option>
                <option value="3"
                    {{ isset($indikatorPersepsi) && $indikatorPersepsi->indikator_persepsi == '3' ? 'selected' : (old('indikator_persepsi') == '3' ? 'selected' : '') }}>
                    3</option>
                <option value="4"
                    {{ isset($indikatorPersepsi) && $indikatorPersepsi->indikator_persepsi == '4' ? 'selected' : (old('indikator_persepsi') == '4' ? 'selected' : '') }}>
                    4</option>
            </select>
            @error('indikator_persepsi')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="kriteria-persepsi">{{ __('Predikat Persepsi') }}</label>
            <select class="form-select @error('kriteria_persepsi') is-invalid @enderror" name="kriteria_persepsi"
                id="kriteria-persepsi" class="form-control" required>
                <option value="" selected disabled>-- {{ __('Select Predikat Persepsi') }} --</option>
                <option value="Sangat tidak setuju"
                    {{ isset($indikatorPersepsi) && $indikatorPersepsi->kriteria_persepsi == 'Sangat tidak setuju' ? 'selected' : (old('kriteria_persepsi') == 'Sangat tidak setuju' ? 'selected' : '') }}>
                    Sangat tidak setuju</option>
                <option value="Tidak setuju"
                    {{ isset($indikatorPersepsi) && $indikatorPersepsi->kriteria_persepsi == 'Tidak setuju' ? 'selected' : (old('kriteria_persepsi') == 'Tidak setuju' ? 'selected' : '') }}>
                    Tidak setuju</option>
                <option value="Setuju"
                    {{ isset($indikatorPersepsi) && $indikatorPersepsi->kriteria_persepsi == 'Setuju' ? 'selected' : (old('kriteria_persepsi') == 'Setuju' ? 'selected' : '') }}>
                    Setuju</option>
                <option value="Sangat setuju"
                    {{ isset($indikatorPersepsi) && $indikatorPersepsi->kriteria_persepsi == 'Sangat setuju' ? 'selected' : (old('kriteria_persepsi') == 'Sangat setuju' ? 'selected' : '') }}>
                    Sangat setuju</option>
            </select>
            @error('kriteria_persepsi')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>
