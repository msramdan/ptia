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
                            <form action="" method="POST">
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
                                        <tr style="background: #1a375e; color: #fff;">
                                            <th colspan="4">LEVEL 3 (Data Primer)</th>
                                        </tr>
                                        @foreach ($level3 as $index => $bobot)
                                            <tr>
                                                <td>{{ $bobot->aspek_nama ?? 'Tidak Ada Nama' }}</td>
                                                <td>
                                                    <div class="input-group ">
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" style="width: 60px;"
                                                            value="{{ $bobot->bobot_alumni }}" />
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group ">
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" style="width: 60px;"
                                                            value="{{ $bobot->bobot_atasan_langsung }}" />
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td style="text-align: center; font-weight: bold;">
                                                <div class="input-group ">
                                                    <input type="number" step="0.01"
                                                        class="form-control form-control-sm" style="width: 60px;" readonly
                                                        value="100" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>
                                            <td style="text-align: center; font-weight: bold;">
                                                <div class="input-group ">
                                                    <input type="number" step="0.01"
                                                        class="form-control form-control-sm" style="width: 60px;" readonly
                                                        value="100" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="background-color: #2e7d32 ; text-align: center; font-weight: bold;"
                                                colspan="2">
                                                <span style="color:#fff;">100 %</span>
                                            </td>
                                        </tr>

                                        {{-- LEVEL 4 --}}
                                        <tr style="background: #1a375e; color: #fff;">
                                            <th colspan="4">LEVEL 4 (Data Primer & Sekunder)</th>
                                        </tr>
                                        @foreach ($level4 as $index => $bobot)
                                            <tr>
                                                <td>Data Primer : {{ $bobot->aspek_nama ?? 'Tidak Ada Nama' }}</td>
                                                <td>
                                                    <div class="input-group ">
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" style="width: 60px;"
                                                            value="{{ $bobot->bobot_alumni }}" />
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group ">
                                                        <input type="number" step="0.01"
                                                            class="form-control form-control-sm" style="width: 60px;"
                                                            value="{{ $bobot->bobot_atasan_langsung }}" />
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td>Data Sekunder : Hasil Pelatihan</td>
                                            <td>
                                                <div class="input-group ">
                                                    <input type="number" step="0.01"
                                                        class="form-control form-control-sm" style="width: 60px;"
                                                        value="12" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group ">
                                                    <input type="number" step="0.01"
                                                        class="form-control form-control-sm" style="width: 60px;"
                                                        value="12" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="text-align: center; font-weight: bold;">
                                                <div class="input-group ">
                                                    <input type="number" step="0.01"
                                                        class="form-control form-control-sm" style="width: 60px;" readonly
                                                        value="100" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>
                                            <td style="text-align: center; font-weight: bold;">
                                                <div class="input-group ">
                                                    <input type="number" step="0.01"
                                                        class="form-control form-control-sm" style="width: 60px;" readonly
                                                        value="100" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="background-color: #c62828 ; text-align: center; font-weight: bold;"
                                                colspan="2">
                                                <span style="color:#fff;">100 %</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <a href="{{ route('project.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                                </a>
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
@endpush
