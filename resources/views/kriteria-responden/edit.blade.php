@extends('layouts.app')

@section('title', __('Edit Kriteria Responden'))

@section('content')
    <style>
        /* Overlay untuk loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            /* Vertikal center */
            justify-content: center;
            /* Horizontal center */
            z-index: 1000;
            display: none;
        }

        /* Wrapper untuk spinner agar bisa diatur lebih rapi */
        .loading-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Animasi Spinner */
        .spinner-border {
            width: 4rem;
            height: 4rem;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Keyframes untuk animasi */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Tambahan teks di bawah spinner */
        .loading-text {
            color: white;
            font-size: 1.2rem;
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-content">
            <div class="spinner-border"></div>
            <div class="loading-text">Loading data...</div>
        </div>
    </div>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Kriteria Responden') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Edit data kriteria responden.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Kriteria Responden') }}
                    </li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <div class="form-group">
                                        <label for="filter_diklat_type">{{ __('Diklat Type') }}</label>
                                        <select class="form-select" name="filter_diklat_type" id="filter_diklat_type"
                                            required>
                                            @foreach ($diklatTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('filter_diklat_type', 1) == $type->id ? 'selected' : '' }}>
                                                    {{ $type->nama_diklat_type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('kriteria-responden.update', $kriteriaResponden->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-2" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                                    <div class="col-md-6 mb-2">
                                        <p>Nilai Post Test</p>

                                        @php
                                            $selectedNilaiPostTest = old(
                                                'nilai_post_test',
                                                $kriteriaResponden->nilai_post_test ?? [],
                                            );
                                        @endphp

                                        @foreach (['Turun', 'Tetap', 'Naik'] as $option)
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input @error('nilai_post_test') is-invalid @enderror"
                                                    type="checkbox" name="nilai_post_test[]" id="{{ strtolower($option) }}"
                                                    value="{{ $option }}"
                                                    {{ in_array($option, $selectedNilaiPostTest) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="{{ strtolower($option) }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach

                                        @error('nilai_post_test')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label
                                                for="nilai-post-test-minimal">{{ __('Nilai Post Test Minimal') }}</label>
                                            <input type="number" name="nilai_post_test_minimal"
                                                id="nilai-post-test-minimal"
                                                class="form-control @error('nilai_post_test_minimal') is-invalid @enderror"
                                                value="{{ old('nilai_post_test_minimal', $kriteriaResponden->nilai_post_test_minimal) }}"
                                                placeholder="{{ __('Nilai Post Test Minimal') }}" required />
                                            @error('nilai_post_test_minimal')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                    {{ __('Update') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            @if (session('success'))
                toastr.success("{{ session('success') }}", "Success", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 5000,
                });
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}", "Error", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 5000,
                });
            @endif
        });
    </script>
    <script>
        $(document).ready(function() {
            let baseUrl = "{{ url('kriteria-responden') }}";
            let diklatTypeDropdown = $('#filter_diklat_type');
            let form = $('form'); // Form utama
            let loadingOverlay = $('#loading-overlay'); // Loading indicator

            function updateUrlParam(diklatType) {
                let newUrl = baseUrl + "?diklatType=" + diklatType;
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);
            }

            function showLoading() {
                loadingOverlay.show();
            }

            function hideLoading() {
                loadingOverlay.hide();
            }

            function fetchKriteriaResponden(diklatTypeId) {
                showLoading(); // Tampilkan indikator loading

                $.ajax({
                    url: baseUrl + "?diklatType=" + diklatTypeId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        if (data.kriteria_responden_id) {
                            form.attr("action", "{{ url('kriteria-responden') }}/" + data
                                .kriteria_responden_id);
                        }

                        if (data.nilai_post_test) {
                            $('input[type=checkbox]').prop('checked', false);
                            data.nilai_post_test.forEach(function(val) {
                                $('#' + val.toLowerCase()).prop('checked', true);
                            });
                        }

                        $('#nilai-post-test-minimal').val(data.nilai_post_test_minimal);
                    },
                    error: function() {
                        alert('Data tidak ditemukan');
                    },
                    complete: function() {
                        hideLoading(); // Sembunyikan indikator loading setelah selesai
                    }
                });
            }

            // Ambil diklatType dari URL jika ada
            let urlParams = new URLSearchParams(window.location.search);
            let diklatTypeFromUrl = urlParams.get('diklatType');

            if (diklatTypeFromUrl) {
                diklatTypeDropdown.val(diklatTypeFromUrl).trigger('change');
            } else {
                let firstDiklatType = diklatTypeDropdown.val();
                updateUrlParam(firstDiklatType);
                fetchKriteriaResponden(firstDiklatType);
            }

            // Event saat select berubah
            diklatTypeDropdown.on('change', function() {
                let diklatTypeId = $(this).val();
                updateUrlParam(diklatTypeId);
                fetchKriteriaResponden(diklatTypeId);
            });
        });
    </script>
@endpush
