@extends('layouts.app')

@section('title', __('Responden'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Responden') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah daftar responden dari project.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('project.index') }}">{{ __('Management Project') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Responden') }}</li>
                </x-breadcrumb>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('project.responden.update', $kriteriaResponden->id) }}" method="POST">
                                <input type="hidden" name="project_id" id="project_id"
                                    class="form-control @error('project_id') is-invalid @enderror"
                                    value="{{ isset($kriteriaResponden) ? $kriteriaResponden->project_id : old('project_id') }}"
                                    required />
                                <input type="hidden" name="kaldikID" id="kaldikID"
                                    class="form-control @error('kaldikID') is-invalid @enderror"
                                    value="{{ isset($kriteriaResponden) ? $project->kaldikID : old('kaldikID') }}"
                                    required />

                                @csrf
                                @method('PUT')
                                <h5>Filter Responden</h5>
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
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label
                                                for="nilai-post-test-minimal">{{ __('Nilai Post Test Minimal') }}</label>
                                            <input type="number" name="nilai_post_test_minimal"
                                                id="nilai-post-test-minimal"
                                                class="form-control @error('nilai_post_test_minimal') is-invalid @enderror"
                                                value="{{ isset($kriteriaResponden) ? $kriteriaResponden->nilai_post_test_minimal : old('nilai_post_test_minimal') }}"
                                                placeholder="{{ __('Nilai Post Test Minimal') }}" required />
                                            @error('nilai_post_test_minimal')
                                                <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('project.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                                </a>
                                @if ($project->status == 'Persiapan')
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                        {{ __('Submit') }}</button>
                                @endif
                            </form>

                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Daftar Responden</h5>
                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Nama peserta') }}</th>
                                            <th>{{ __('NIP') }}</th>
                                            <th>{{ __('No.Telepon') }}</th>
                                            <th>{{ __('Jabatan') }}</th>
                                            <th>{{ __('Unit') }}</th>
                                            <th>{{ __('Nilai Post Test') }}</th>
                                            <th>{{ __('Nilai Kenaikan Pre Post') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </section>

    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.js"></script>
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
            var projectId = @json($project->id);
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('project.responden.show', ':id') }}".replace(':id', projectId),
                    data: function(d) {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                    },
                    {
                        data: 'nip',
                        name: 'nip',
                    },
                    {
                        data: 'telepon',
                        name: 'telepon',
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan',
                    },
                    {
                        data: 'unit',
                        name: 'unit',
                    },
                    {
                        data: 'nilai_pre_test',
                        name: 'nilai_pre_test',
                    },
                    {
                        data: 'nilai_post_test',
                        name: 'nilai_post_test',
                    },
                ],
            });
        });
    </script>
@endpush
