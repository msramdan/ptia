@extends('layouts.app')

@section('title', __('Penyebaran Kuesioner'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Penyebaran Kuesioner') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah daftar semua Penyebaran Kuesioner.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Penyebaran Kuesioner') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            @can('project create')
                <div class="d-flex justify-content-end">
                    <a href="{{ route('project.create') }}" class="btn btn-primary mb-3">
                        <i class="fas fa-plus"></i>
                        {{ __('Tambah data project') }}
                    </a>
                </div>
            @endcan

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">#</th>
                                            <th rowspan="2">{{ __('Dibuat Oleh') }}</th>
                                            <th rowspan="2">{{ __('Kode Diklat') }}</th>
                                            <th rowspan="2">{{ __('Nama Diklat') }}</th>
                                            <th rowspan="2">{{ __('Jenis Diklat') }}</th>
                                            <th colspan="2" class="text-center">{{ __('Alumni') }}</th>
                                            <th colspan="2" class="text-center">{{ __('Atasan') }}</th>
                                            <th rowspan="2">{{ __('Aksi') }}</th>
                                        </tr>

                                        <tr>
                                            <th class="text-center">Responden</th>
                                            <th class="text-center">Keterisian</th>
                                            <th class="text-center">Responden</th>
                                            <th class="text-center">Keterisian</th>
                                        </tr>
                                    </thead>
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
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('penyebaran-kuesioner.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'user',
                    name: 'user',
                },
                {
                    data: 'kaldikID',
                    name: 'kaldikID',
                },
                {
                    data: 'kaldikDesc',
                    name: 'kaldikDesc',
                },
                {
                    data: 'nama_diklat_type',
                    name: 'diklat_type.nama_diklat_type',
                },
                {
                    data: 'responden_alumni',
                    name: 'responden_alumni',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'keterisian_alumni',
                    name: 'keterisian_alumni',
                    className: 'text-center',
                },
                {
                    data: 'responden_atasan',
                    name: 'responden_atasan',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'keterisian_atasan',
                    name: 'keterisian_atasan',
                    className: 'text-center',
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center',
                }
            ],
        });
    </script>
@endpush
