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
                        {{ __('Master Data Hasil Evaluasi Pasca Pembelajaran') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Hasil Evaluasi') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('No') }}</th>
                                            <th>{{ __('Nama Project') }}</th>
                                            <th>{{ __('Kode Project') }}</th>
                                            <th colspan="2" class="text-center">{{ __('Level 3') }}</th>
                                            <th colspan="2" class="text-center">{{ __('Level 4') }}</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>{{ __('Skor') }}</th>
                                            <th>{{ __('Predikat') }}</th>
                                            <th>{{ __('Skor') }}</th>
                                            <th>{{ __('Predikat') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan diisi oleh DataTables via AJAX -->
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
                        data: 'nama_project',
                        name: 'nama_project'
                    },
                    {
                        data: 'kode_project',
                        name: 'kode_project'
                    },
                    {
                        data: 'skor_level_3',
                        name: 'skor_level_3'
                    },
                    {
                        data: 'predikat_level_3',
                        name: 'predikat_level_3'
                    },
                    {
                        data: 'skor_level_4',
                        name: 'skor_level_4'
                    },
                    {
                        data: 'predikat_level_4',
                        name: 'predikat_level_4'
                    },
                ]
            });
        });
    </script>
@endpush
