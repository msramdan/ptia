<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="nilai-minimal">{{ __('Nilai Minimal') }}</label>
            <input type="number" name="nilai_minimal" id="nilai-minimal" class="form-control @error('nilai_minimal') is-invalid @enderror" value="{{ isset($indikatorDampak) ? $indikatorDampak->nilai_minimal : old('nilai_minimal') }}" placeholder="{{ __('Nilai Minimal') }}" required />
            @error('nilai_minimal')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="nilai-maksimal">{{ __('Nilai Maksimal') }}</label>
            <input type="number" name="nilai_maksimal" id="nilai-maksimal" class="form-control @error('nilai_maksimal') is-invalid @enderror" value="{{ isset($indikatorDampak) ? $indikatorDampak->nilai_maksimal : old('nilai_maksimal') }}" placeholder="{{ __('Nilai Maksimal') }}" required />
            @error('nilai_maksimal')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="kriteria-dampak">{{ __('Kriteria Dampak') }}</label>
            <input type="text" name="kriteria_dampak" id="kriteria-dampak" class="form-control @error('kriteria_dampak') is-invalid @enderror" value="{{ isset($indikatorDampak) ? $indikatorDampak->kriteria_dampak : old('kriteria_dampak') }}" placeholder="{{ __('Kriteria Dampak') }}" required />
            @error('kriteria_dampak')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>