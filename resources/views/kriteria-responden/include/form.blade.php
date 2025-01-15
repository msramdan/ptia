<div class="row mb-2" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
    <div class="col-md-6">
        <p>Nilai Post Test</p>
        <div class="form-check mb-2">
            <input class="form-check-input @error('nilai_post_test') is-invalid @enderror" type="radio" name="nilai_post_test" id="turun" value="Turun" {{ isset($kriteriaResponden) && $kriteriaResponden?->nilai_post_test == 'Turun' ? 'checked' : (old('nilai_post_test') == 'Turun' ? 'checked' : '') }} required>
            <label class="form-check-label" for="turun">
                Turun
            </label>
            @error('nilai_post_test')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input @error('nilai_post_test') is-invalid @enderror" type="radio" name="nilai_post_test" id="tetap" value="Tetap" {{ isset($kriteriaResponden) && $kriteriaResponden?->nilai_post_test == 'Tetap' ? 'checked' : (old('nilai_post_test') == 'Tetap' ? 'checked' : '') }} required>
            <label class="form-check-label" for="tetap">
                Tetap
            </label>
            @error('nilai_post_test')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="form-check mb-2">
            <input class="form-check-input @error('nilai_post_test') is-invalid @enderror" type="radio" name="nilai_post_test" id="naik" value="Naik" {{ isset($kriteriaResponden) && $kriteriaResponden?->nilai_post_test == 'Naik' ? 'checked' : (old('nilai_post_test') == 'Naik' ? 'checked' : '') }} required>
            <label class="form-check-label" for="naik">
                Naik
            </label>
            @error('nilai_post_test')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="nilai-pre-test-minimal">{{ __('Nilai Pre Test Minimal') }}</label>
            <input type="number" name="nilai_pre_test_minimal" id="nilai-pre-test-minimal" class="form-control @error('nilai_pre_test_minimal') is-invalid @enderror" value="{{ isset($kriteriaResponden) ? $kriteriaResponden->nilai_pre_test_minimal : old('nilai_pre_test_minimal') }}" placeholder="{{ __('Nilai Pre Test Minimal') }}" required />
            @error('nilai_pre_test_minimal')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="nilai-post-test-minimal">{{ __('Nilai Post Test Minimal') }}</label>
            <input type="number" name="nilai_post_test_minimal" id="nilai-post-test-minimal" class="form-control @error('nilai_post_test_minimal') is-invalid @enderror" value="{{ isset($kriteriaResponden) ? $kriteriaResponden->nilai_post_test_minimal : old('nilai_post_test_minimal') }}" placeholder="{{ __('Nilai Post Test Minimal') }}" required />
            @error('nilai_post_test_minimal')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="nilai-kenaikan-pre-post">{{ __('Nilai Kenaikan Pre Post') }}</label>
            <input type="number" name="nilai_kenaikan_pre_post" id="nilai-kenaikan-pre-post" class="form-control @error('nilai_kenaikan_pre_post') is-invalid @enderror" value="{{ isset($kriteriaResponden) ? $kriteriaResponden->nilai_kenaikan_pre_post : old('nilai_kenaikan_pre_post') }}" placeholder="{{ __('Nilai Kenaikan Pre Post') }}" required />
            @error('nilai_kenaikan_pre_post')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>
