@extends('layouts.app')

@section('title', __('Data Sekunder'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Data Sekunder') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah daftar semua Data Sekunder.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Data Sekunder') }}</li>
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
                                            <th>#</th>
                                            <th>{{ __('Dibuat Oleh') }}</th>
                                            <th>{{ __('Kode Diklat') }}</th>
                                            <th>{{ __('Nama Diklat') }}</th>
                                            <th>{{ __('Jenis Diklat') }}</th>
                                            <th>{{ __('Data Sekunder') }}</th>
                                            <th>{{ __('Berkas') }}</th>
                                            <th>{{ __('Aksi') }}</th>
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

    <!-- Modal Statis untuk Input Data Sekunder -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- Ubah ke modal-lg untuk lebih lebar -->
            <div class="modal-content">
                <form action="{{ route('data-sekunder.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="project_id" id="project_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Data Sekunder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nilai_kinerja_awal" class="form-label">Kinerja Awal</label>
                                <input type="number" class="form-control" id="nilai_kinerja_awal" name="nilai_kinerja_awal"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="periode_awal" class="form-label">Periode Awal</label>
                                <input type="text" class="form-control" id="periode_awal" name="periode_awal" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nilai_kinerja_akhir" class="form-label">Kinerja Akhir</label>
                                <input type="number" class="form-control" id="nilai_kinerja_akhir"
                                    name="nilai_kinerja_akhir" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="periode_akhir" class="form-label">Periode Akhir</label>
                                <input type="text" class="form-control" id="periode_akhir" name="periode_akhir" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
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
                            <div class="col-md-6 mb-3">
                                <label for="sumber_data" class="form-label">Sumber Data</label>
                                <input type="text" class="form-control" id="sumber_data" name="sumber_data" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unit_kerja" class="form-label">Unit Kerja</label>
                                <input type="text" class="form-control" id="unit_kerja" name="unit_kerja" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_pic" class="form-label">Nama PIC</label>
                                <input type="text" class="form-control" id="nama_pic" name="nama_pic" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telpon" class="form-label">Telpon</label>
                                <input type="text" class="form-control" id="telpon" name="telpon" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="berkas" class="form-label">Upload Berkas</label>
                                <input type="file" class="form-control" id="berkas" name="berkas"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png,.ppt,.pptx">
                                <small class="text-muted">Format yang diperbolehkan: PDF, Word, Excel, JPG, PNG, PowerPoint</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
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

            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('data-sekunder.index') }}",
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
                        data: 'data_sekunder',
                        name: 'data_sekunder',
                        className: 'text-center',
                    },
                    {
                        data: 'berkas',
                        name: 'berkas',
                    },
                    {
                        data: 'action',
                        name: 'action',
                    }
                ]
            });

            // Event untuk membuka modal dan mengisi project_id
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

        });
    </script>
@endpush
