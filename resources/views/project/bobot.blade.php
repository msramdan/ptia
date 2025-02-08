@extends('layouts.app')

@section('title', __('Edit Bobot Aspek'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Bobot Aspek') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Edit data bobot aspek.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('project.index') }}">{{ __('Management Project') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Bobot Aspek') }}</li>
                </x-breadcrumb>

            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Kode Diklat</strong></td>
                                    <td>: {{ $project->kaldikID ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Diklat</strong></td>
                                    <td>: {{ $project->kaldikDesc ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $project->user_name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('bobot-aspek.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Aspek</th>
                                            <th style="width: 160px;">Bobot Alumni</th>
                                            <th style="width: 160px;">Bobot Atasan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- LEVEL 3 -->
                                        <tr style="background: #1a375e; color: #fff;">
                                            <th colspan="3">LEVEL 3 (Data Primer)</th>
                                        </tr>
                                        @foreach ($level3 as $index => $bobot)
                                            <tr>
                                                <td>{{ $bobot->aspek_nama ?? 'Tidak Ada Nama' }}</td>
                                                <td>
                                                    <input type="hidden" name="level3[{{ $index }}][id]"
                                                        value="{{ $bobot->id }}">
                                                    <div class="input-group">
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm level3-alumni"
                                                            name="level3[{{ $index }}][bobot_alumni]"
                                                            value="{{ $bobot->bobot_alumni }}" oninput="calculateTotal()" />
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm level3-atasan"
                                                            name="level3[{{ $index }}][bobot_atasan_langsung]"
                                                            value="{{ $bobot->bobot_atasan_langsung }}"
                                                            oninput="calculateTotal()" />
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>


                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td id="total-level3-alumni" class="total-percentage"
                                                style="text-align: center; font-weight: bold;">0%</td>
                                            <td id="total-level3-atasan" class="total-percentage"
                                                style="text-align: center; font-weight: bold;">0%</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td id="final-total-level3" class="total-final" colspan="2"
                                                style="text-align: center; font-weight: bold;">0%</td>
                                        </tr>

                                        <!-- LEVEL 4 -->
                                        <tr style="background: #1a375e; color: #fff;">
                                            <th colspan="3">LEVEL 4 (Data Primer & Sekunder)</th>
                                        </tr>
                                        @foreach ($level4 as $index => $bobot)
                                            <tr>
                                                <td>Data Primer : {{ $bobot->aspek_nama ?? 'Tidak Ada Nama' }}</td>
                                                <td>
                                                    <input type="hidden" name="level4[{{ $index }}][id]"
                                                        value="{{ $bobot->id }}">
                                                    <div class="input-group">
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm level4-alumni"
                                                            name="level4[{{ $index }}][bobot_alumni]"
                                                            value="{{ $bobot->bobot_alumni }}"
                                                            oninput="calculateTotal()" />
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm level4-atasan"
                                                            name="level4[{{ $index }}][bobot_atasan_langsung]"
                                                            value="{{ $bobot->bobot_atasan_langsung }}"
                                                            oninput="calculateTotal()" />
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- Tambahan Data Sekunder -->
                                        <tr>
                                            <td>Data Sekunder : Hasil Pelatihan</td>
                                            <td colspan="2">
                                                <input type="hidden" name="bobot_aspek_sekunder_id"
                                                    value="{{ $dataSecondary->id }}">
                                                <div class="input-group">
                                                    <input type="number" step="0.01"
                                                        class="form-control form-control-sm level4-alumni"
                                                        name="bobot_aspek_sekunder"
                                                        value="{{ $dataSecondary->bobot_aspek_sekunder ?? 0 }}"
                                                        oninput="calculateTotal()" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td id="total-level4-alumni" class="total-percentage"
                                                style="text-align: center; font-weight: bold;">0%</td>
                                            <td id="total-level4-atasan" class="total-percentage"
                                                style="text-align: center; font-weight: bold;">0%</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td id="final-total-level4" class="total-final" colspan="2"
                                                style="text-align: center; font-weight: bold;">0%</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <a href="{{ route('project.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                                </a>
                                <button type="submit" id="submit-btn" class="btn btn-primary"><i
                                        class="fas fa-save"></i>
                                    Update</button>
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
        function calculateTotal() {
            let totalAlumniLevel3 = 0,
                totalAtasanLevel3 = 0;
            let totalAlumniLevel4 = 0,
                totalAtasanLevel4 = 0;

            // Hitung total untuk Level 3
            document.querySelectorAll('.level3-alumni').forEach(input => {
                totalAlumniLevel3 += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('.level3-atasan').forEach(input => {
                totalAtasanLevel3 += parseFloat(input.value) || 0;
            });

            let finalTotalLevel3 = totalAlumniLevel3 + totalAtasanLevel3;

            // Hitung total untuk Level 4
            document.querySelectorAll('.level4-alumni').forEach(input => {
                totalAlumniLevel4 += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('.level4-atasan').forEach(input => {
                totalAtasanLevel4 += parseFloat(input.value) || 0;
            });

            let finalTotalLevel4 = totalAlumniLevel4 + totalAtasanLevel4;

            // Menampilkan total di tabel
            document.getElementById('total-level3-alumni').innerText = totalAlumniLevel3.toFixed(2) + '%';
            document.getElementById('total-level3-atasan').innerText = totalAtasanLevel3.toFixed(2) + '%';
            document.getElementById('final-total-level3').innerText = finalTotalLevel3.toFixed(2) + '%';

            document.getElementById('total-level4-alumni').innerText = totalAlumniLevel4.toFixed(2) + '%';
            document.getElementById('total-level4-atasan').innerText = totalAtasanLevel4.toFixed(2) + '%';
            document.getElementById('final-total-level4').innerText = finalTotalLevel4.toFixed(2) + '%';

            // Update warna berdasarkan nilai
            updateBackground('final-total-level3', finalTotalLevel3);
            updateBackground('final-total-level4', finalTotalLevel4);

            // Nonaktifkan tombol submit jika final-total-level3 atau final-total-level4 tidak sama dengan 100%
            let submitBtn = document.getElementById('submit-btn');
            if (finalTotalLevel3 !== 100 || finalTotalLevel4 !== 100) {
                submitBtn.disabled = true;
            } else {
                submitBtn.disabled = false;
            }
        }

        function updateBackground(elementId, value) {
            let element = document.getElementById(elementId);
            if (value > 100 || value < 100) {
                element.style.backgroundColor = 'red';
                element.style.color = 'white';
            } else {
                element.style.backgroundColor = 'green';
                element.style.color = 'white';
            }
        }

        // Jalankan perhitungan saat halaman dimuat
        document.addEventListener("DOMContentLoaded", calculateTotal);
    </script>
@endpush
