<!-- resources/views/hasil-evaluasi/index.blade.php -->
@extends('layouts.app')

@section('title', __('Hasil Evaluasi'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Hasil Evaluasi Pasca Pembelajaran') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Hasil Evaluasi Pasca Pembelajaran') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Hasil Evaluasi') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('hasil-evaluasi.export-excel') }}" class="btn btn-success mb-4">
                                <i class="fas fa-file-excel"></i> Export ke Excel
                            </a>
                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">#</th>
                                            <th rowspan="2">{{ __('Evaluator') }}</th>
                                            <th rowspan="2">{{ __('Kode Diklat') }}</th>
                                            <th rowspan="2">{{ __('Nama Diklat') }}</th>
                                            <th rowspan="2">{{ __('Jenis Diklat') }}</th>
                                            <th colspan="2" class="text-center">{{ __('Level 3') }}</th>
                                            <th colspan="2" class="text-center">{{ __('Level 4') }}</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Skor</th>
                                            <th class="text-center">Predikat</th>
                                            <th class="text-center">Skor</th>
                                            <th class="text-center">Predikat</th>
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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('hasil-evaluasi.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'kaldikID',
                        name: 'kaldikID'
                    },
                    {
                        data: 'nama_project',
                        name: 'nama_project'
                    },
                    {
                        data: 'nama_diklat_type',
                        name: 'nama_diklat_type'
                    },
                    {
                        data: 'avg_skor_level_3',
                        name: 'avg_skor_level_3'
                    },
                    {
                        data: 'kriteria_dampak_level_3',
                        name: 'kriteria_dampak_level_3'
                    },
                    {
                        data: 'avg_skor_level_4',
                        name: 'avg_skor_level_4'
                    },
                    {
                        data: 'kriteria_dampak_level_4',
                        name: 'kriteria_dampak_level_4'
                    }
                ]
            });
        });
    </script>
@endpush
