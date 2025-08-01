@extends('layouts.app')

@section('title', __('Data Sekunder'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Data Sekunder') }}</h3>
                    <p class="text-subtitle text-muted">
                        Input dan lihat data sekunder per project.
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Data Sekunder') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
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
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <label for="filter_status_data_sekunder"
                                        class="form-label">{{ __('Status Data Sekunder') }}</label>
                                    <select class="form-select" id="filter_status_data_sekunder">
                                        <option value="">{{ __('Semua Status') }}</option>
                                        <option value="Meningkat"
                                            {{ request('status_data_sekunder') == 'Meningkat' ? 'selected' : '' }}>
                                            {{ __('Meningkat') }}</option>
                                        <option value="Tetap"
                                            {{ request('status_data_sekunder') == 'Tetap' ? 'selected' : '' }}>
                                            {{ __('Tetap') }}</option>
                                        <option value="Menurun"
                                            {{ request('status_data_sekunder') == 'Menurun' ? 'selected' : '' }}>
                                            {{ __('Menurun') }}</option>
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
                                            <th class="text-center">{{ __('Data Sekunder') }}</th>
                                            <th class="text-center">{{ __('Berkas') }}</th>
                                            <th class="text-center">{{ __('Aksi') }}</th>
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
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('data-sekunder.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" id="project_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Data Sekunder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger"><i>Note: * Wajib diisi</i></p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nilai_kinerja_awal" class="form-label">Kinerja Awal <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="nilai_kinerja_awal" name="nilai_kinerja_awal"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="periode_awal" class="form-label">Periode Awal <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="periode_awal" name="periode_awal" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nilai_kinerja_akhir" class="form-label">Kinerja Akhir <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="nilai_kinerja_akhir"
                                    name="nilai_kinerja_akhir" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="periode_akhir" class="form-label">Periode Akhir <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="periode_akhir" name="periode_akhir" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="satuan" class="form-label">Satuan <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="satuan" name="satuan" required>
                                    <option value="Persentase (%)">Persentase (%)</option>
                                    <option value="Skor">Skor</option>
                                    <option value="Rupiah (Rp)">Rupiah (Rp)</option>
                                    <option value="Waktu (Jam)">Waktu (Jam)</option>
                                    <option value="Waktu (Hari)">Waktu (Hari)</option>
                                    <option value="Pcs">Pcs</option>
                                    <option value="Unit">Unit</option>
                                    <option value="Item">Item</option>
                                    <option value="Dollar (USD)">Dollar (USD)</option>
                                    <option value="Index">Index</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sumber_data" class="form-label">Sumber Data <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sumber_data" name="sumber_data" required>
                            </div>
                            <div class="col-md-4 mb-3 position-relative">
                                <label for="berkas" class="form-label">
                                    Upload Berkas
                                    <i class="fa fa-info-circle text-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="Format yang diperbolehkan: PDF, Word, Excel, JPG, PNG, PowerPoint"></i>
                                </label>
                                <input type="file" class="form-control" id="berkas" name="berkas"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png,.ppt,.pptx">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="unit_kerja" class="form-label">Unit Kerja <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unit_kerja" name="unit_kerja" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nama_pic" class="form-label">Nama PIC <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_pic" name="nama_pic" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="telpon" class="form-label">Telpon <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="telpon" name="telpon" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="keterangan" class="form-label">Keterangan <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
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
                var statusDataSekunder = $('#filter_status_data_sekunder').val(); // Ambil nilai filter status

                if ($.fn.DataTable.isDataTable('#data-table')) {
                    dataTable.destroy();
                }

                dataTable = $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 100,
                    ajax: {
                        url: "{{ route('data-sekunder.index') }}",
                        type: "GET",
                        data: function(d) {
                            d.evaluator = evaluator;
                            d.diklat_type = diklatType;
                            d.status_data_sekunder = statusDataSekunder; // Kirim parameter status
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
                            name: 'project.tanggal_selesai',
                        },
                        {
                            data: 'kaldikID',
                            name: 'project.kaldikID'
                        },
                        {
                            data: 'kaldikDesc',
                            name: 'project.kaldikDesc'
                        },
                        {
                            data: 'nama_diklat_type',
                            name: 'diklat_type.nama_diklat_type'
                        },
                        {
                            data: 'data_sekunder',
                            name: 'data_sekunder',
                            className: 'text-center',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'berkas',
                            name: 'berkas',
                            className: 'text-center',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            className: 'text-center',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }

            function updateUrl() {
                var evaluator = $('#filter_evaluator').val();
                var diklatType = $('#filter_diklat_type').val();
                var statusDataSekunder = $('#filter_status_data_sekunder').val();

                var params = new URLSearchParams();
                if (evaluator) params.append('evaluator', evaluator);
                if (diklatType) params.append('diklat_type', diklatType);
                if (statusDataSekunder) params.append('status_data_sekunder', statusDataSekunder);


                var newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                history.pushState(null, '', newUrl);
                loadDataTable();
            }

            $('#filter_evaluator, #filter_diklat_type, #filter_status_data_sekunder').on('change', function() {
                updateUrl();
            });

            loadDataTable();
        });

        $(document).on('click', '.btn-action', function() {
            var projectId = $(this).data('id');
            $('#project_id').val(projectId);
            $.ajax({
                url: '/data-sekunder/get/' + projectId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#nilai_kinerja_awal').val(response.data.nilai_kinerja_awal);
                        $('#periode_awal').val(response.data.periode_awal);
                        $('#nilai_kinerja_akhir').val(response.data.nilai_kinerja_akhir);
                        $('#periode_akhir').val(response.data.periode_akhir);
                        $('#sumber_data').val(response.data.sumber_data);
                        $('#unit_kerja').val(response.data.unit_kerja);
                        $('#nama_pic').val(response.data.nama_pic);
                        $('#telpon').val(response.data.telpon);
                        $('#keterangan').val(response.data.keterangan);
                        $('#satuan').val(response.data.satuan).change();
                    } else {
                        $('#nilai_kinerja_awal, #periode_awal, #nilai_kinerja_akhir, #periode_akhir, #sumber_data, #unit_kerja, #nama_pic, #telpon, #keterangan')
                            .val('');
                        $('#satuan').val('').change();
                    }
                    $('#createModal').modal('show');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
