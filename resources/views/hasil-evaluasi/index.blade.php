@extends('layouts.app')

@section('title', __('Hasil Evaluasi'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Hasil Evaluasi') }}</h3>
                    <p class="text-subtitle text-muted">
                        Lihat hasil evaluasi proyek berdasarkan skor level 3 dan 4.
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Hasil Evaluasi') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="filter_evaluator" class="form-label">{{ __('Evaluator') }}</label>
                                    <select class="form-select" id="filter_evaluator">
                                        <option value="">{{ __('Semua Evaluator') }}</option>
                                        @foreach ($evaluators as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('evaluator') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filter_diklat_type" class="form-label">{{ __('Jenis Diklat') }}</label>
                                    <select class="form-select" id="filter_diklat_type">
                                        <option value="">{{ __('Semua Jenis Diklat') }}</option>
                                        @foreach ($diklatTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ request('diklat_type') == $type->id ? 'selected' : '' }}>
                                                {{ $type->nama_diklat_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>{{ __('Evaluator') }}</th>
                                            <th>{{ __('Tgl Generate') }}</th>
                                            <th>{{ __('Tgl Selesai Diklat') }}</th>
                                            <th>{{ __('Kode Diklat') }}</th>
                                            <th>{{ __('Nama Diklat') }}</th>
                                            <th>{{ __('Jenis Diklat') }}</th>
                                            <th>{{ __('Skor Level 3') }}</th>
                                            <th>{{ __('Kriteria Dampak Level 3') }}</th>
                                            <th>{{ __('Skor Level 4') }}</th>
                                            <th>{{ __('Kriteria Dampak Level 4') }}</th>
                                            <th>{{ __('Aksi') }}</th>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/combine/npm/datatables.net@1.12.0,npm/datatables.net-bs5@1.12.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            @if (session('success'))
                toastr.success("{{ session('success') }}", "Success", {
                    positionClass: "toast-top-right",
                    timeOut: 3000
                });
            @endif
            @if (session('error'))
                toastr.error("{{ session('error') }}", "Error", {
                    positionClass: "toast-top-right",
                    timeOut: 5000
                });
            @endif

            var dataTable;

            function loadDataTable() {
                var evaluator = $('#filter_evaluator').val();
                var diklatType = $('#filter_diklat_type').val();

                if ($.fn.DataTable.isDataTable('#data-table')) {
                    dataTable.destroy();
                }

                dataTable = $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 100,
                    ajax: {
                        url: "{{ route('hasil-evaluasi.index') }}",
                        type: "GET",
                        data: function(d) {
                            d.evaluator = evaluator;
                            d.diklat_type = diklatType;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'user',
                            name: 'users.name'
                        },
                        {
                            data: 'created_at',
                            name: 'project.created_at'
                        },
                        {
                            data: 'tanggal_selesai',
                            name: 'project.tanggal_selesai'
                        },
                        {
                            data: 'kaldikID',
                            name: 'project.kaldikID'
                        },
                        {
                            data: 'nama_project',
                            name: 'project.kaldikDesc'
                        },
                        {
                            data: 'nama_diklat_type',
                            name: 'diklat_type.nama_diklat_type'
                        },
                        {
                            data: 'avg_skor_level_3',
                            name: 'avg_skor_level_3',
                            className: 'text-center'
                        },
                        {
                            data: 'kriteria_dampak_level_3',
                            name: 'kriteria_dampak_level_3',
                            className: 'text-center'
                        },
                        {
                            data: 'avg_skor_level_4',
                            name: 'avg_skor_level_4',
                            className: 'text-center'
                        },
                        {
                            data: 'kriteria_dampak_level_4',
                            name: 'kriteria_dampak_level_4',
                            className: 'text-center'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            className: 'text-center'
                        }
                    ]
                });
            }

            function updateUrl() {
                var evaluator = $('#filter_evaluator').val();
                var diklatType = $('#filter_diklat_type').val();

                var params = new URLSearchParams();
                if (evaluator) params.append('evaluator', evaluator);
                if (diklatType) params.append('diklat_type', diklatType);

                var newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                history.pushState(null, '', newUrl);
                loadDataTable();
            }

            $('#filter_evaluator, #filter_diklat_type').on('change', function() {
                updateUrl();
            });

            loadDataTable();
        });
    </script>
@endpush
