<div class="row mb-2">
    <div class="col-md-12">
        <div class="form-group">
            <label for="diklat-type">{{ __('Diklat Type') }}</label>
            <select class="form-select" id="diklat-type" required>
                <option value="" selected disabled>-- {{ __('Select Diklat Type') }} --</option>
                @foreach ($diklatTypes as $diklat)
                    <option value="{{ $diklat->id }}"
                        {{ isset($kuesioner) && $kuesioner->diklat_type_id == $diklat->id ? 'selected' : '' }}>
                        {{ $diklat->nama_diklat_type }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="aspek-id">{{ __('Aspek') }}</label>
            <select class="form-select @error('aspek_id') is-invalid @enderror" name="aspek_id" id="aspek-id" required>
                <option value="" selected disabled>-- {{ __('Select Aspek') }} --</option>
                @foreach ($aspeks as $aspek)
                    <option value="{{ $aspek->id }}" data-diklat="{{ $aspek->diklat_type_id }}"
                        {{ isset($kuesioner) && $kuesioner->aspek_id == $aspek->id ? 'selected' : (old('aspek_id') == $aspek->id ? 'selected' : '') }}>
                        {{ $aspek->aspek }} ({{ $aspek->nama_diklat_type }})
                    </option>
                @endforeach
            </select>
            @error('aspek_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="pertanyaan">{{ __('Pertanyaan') }}</label>
            <textarea name="pertanyaan" id="pertanyaan" class="form-control @error('pertanyaan') is-invalid @enderror"
                rows="5" placeholder="{{ __('Pertanyaan') }}" required>{{ isset($kuesioner) ? $kuesioner->pertanyaan : old('pertanyaan') }}</textarea>
            @error('pertanyaan')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-12 mt-2">
        <div class="alert alert-info">
            <strong>Keterangan Parameter:</strong>
            <ul class="mb-0">
                <li><code>{params_target}</code> akan otomatis digantikan dengan <strong>"Saya"</strong> atau
                    <strong>"Alumni"</strong> di kuesioner project.</li>
                <li><code>{params_nama_diklat}</code> akan otomatis digantikan dengan <strong>Nama Diklat</strong> di
                    kuesioner project.</li>
            </ul>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const diklatTypeSelect = document.getElementById('diklat-type');
        const aspekSelect = document.getElementById('aspek-id');

        function filterAspek() {
            const selectedDiklat = diklatTypeSelect.value;
            Array.from(aspekSelect.options).forEach(option => {
                if (option.value) {
                    option.style.display = option.getAttribute('data-diklat') === selectedDiklat ?
                        'block' : 'none';
                }
            });
        }

        diklatTypeSelect.addEventListener('change', function() {
            filterAspek();
            aspekSelect.value = ''; // Reset pilihan aspek jika diklat type berubah
        });

        // Jalankan filter saat halaman dimuat
        filterAspek();
    });
</script>
