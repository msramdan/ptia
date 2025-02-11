@extends('layouts.app')

@section('title', __('Konversi'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Konversi') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah daftar semua konversi.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Konversi') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            @can('konversi create')
                <div class="d-flex justify-content-end">
                    <a href="{{ route('konversi.create') }}" class="btn btn-primary mb-3">
                        <i class="fas fa-plus"></i>
                        {{ __('Tambah data konversi') }}
                    </a>
                </div>
            @endcan

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <div class="form-group">
                                        <label for="filter_diklat_type">{{ __('Diklat Type') }}</label>
                                        <select class="form-select" name="filter_diklat_type" id="filter_diklat_type">
                                            <option value="">-- {{ __('All') }} --</option>
                                            @foreach ($diklatTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ isset($selectedDiklatType) && $selectedDiklatType == $type->id ? 'selected' : '' }}>
                                                    {{ $type->nama_diklat_type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Diklat Type') }}</th>
                                            <th>{{ __('Jenis Skor') }}</th>
                                            <th>{{ __('Skor') }}</th>
                                            <th>{{ __('Konversi') }}</th>
                                            <th>{{ __('Action') }}</th>
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
        $(document).ready(function() {
            // Ambil parameter diklatType dari URL jika ada
            let urlParams = new URLSearchParams(window.location.search);
            let diklatType = urlParams.get('diklatType');

            if (diklatType) {
                $('#filter_diklat_type').val(diklatType);
            }

            // Inisialisasi DataTables dengan parameter filter dari URL
            let table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('konversi.index') }}",
                    data: function(d) {
                        d.diklatType = $('#filter_diklat_type').val() || null;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_diklat_type',
                        name: 'diklat_type.nama_diklat_type'
                    },
                    {
                        data: 'jenis_skor',
                        name: 'jenis_skor'
                    },
                    {
                        data: 'skor',
                        name: 'skor'
                    },
                    {
                        data: 'konversi',
                        name: 'konversi'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            // Event handler saat filter berubah
            $('#filter_diklat_type').change(function() {
                let selectedValue = $(this).val();
                let newUrl = "{{ route('konversi.index') }}";

                if (selectedValue) {
                    newUrl += '?diklatType=' + selectedValue;
                }

                window.history.pushState({}, '', newUrl); // Ubah URL tanpa reload
                table.ajax.reload(); // Reload DataTables dengan filter baru
            });
        });
    </script>
@endpush
