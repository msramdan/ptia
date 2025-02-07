@extends('layouts.app')

@section('title', __('Pembuatan Project'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Pembuatan Project') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah daftar semua pembuatan Project.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Pembuatan Project') }}</li>
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
                                            <th>{{ __('Kode Diklat') }}</th>
                                            <th>{{ __('Nama Diklat') }}</th>
                                            <th>{{ __('Jenis DIklat') }}</th>
                                            <th>{{ __('Tanggal Diklat') }}</th>
                                            <th>{{ __('Status Generate') }}</th>
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

    <!-- Modal Detail Diklat -->
    <div class="modal fade" id="modalDetailDiklat" tabindex="-1" aria-labelledby="modalDetailDiklatLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailDiklatLabel">{{ __('Detail Diklat') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="detailDiklatTable">
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPesertaDiklat" tabindex="-1" aria-labelledby="modalPesertaDiklatLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPesertaDiklatLabel">{{ __('Peserta Diklat') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="pesertaDiklatTable">
                        <thead>
                            <tr>
                                <th>{{ __('Nama Peserta') }}</th>
                                <th>{{ __('NIP') }}</th>
                                <th>{{ __('No. Telepon') }}</th>
                                <th>{{ __('Jabatan') }}</th>
                                <th>{{ __('Unit') }}</th>
                                <th>{{ __('Nilai Pre-Test') }}</th>
                                <th>{{ __('Nilai Post-Test') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: function(data, callback, settings) {
                    $.ajax({
                        url: "/get-kaldik-data",
                        type: "GET",
                        data: {
                            limit: data.length,
                            page: (data.start / data.length) + 1,
                            search: data.search.value
                        },
                        success: function(response) {
                            callback({
                                recordsTotal: response.total,
                                recordsFiltered: response.total,
                                data: response.data.map(function(item) {
                                    // Function to format date in Y-m-d format
                                    function formatDate(dateString) {
                                        var date = new Date(dateString);
                                        var year = date.getFullYear();
                                        var month = ('0' + (date
                                            .getMonth() + 1)).slice(-2);
                                        var day = ('0' + date.getDate())
                                            .slice(-2);
                                        return `${year}-${month}-${day}`;
                                    }

                                    // Menambahkan status generate
                                    var statusGenerateClass = item
                                        .status_generate === 'SUDAH' ?
                                        'btn-success' : 'btn-danger';
                                    var statusGenerateText = item
                                        .status_generate === 'SUDAH' ?
                                        'SUDAH' : 'BELUM';

                                    return {
                                        kaldikID: item.kaldikID,
                                        kaldikDesc: item.kaldikDesc,
                                        biayaName: item.biayaName,
                                        diklatTypeName: item.diklatTypeName,
                                        dateRange: `${formatDate(item.startDate)} s/d ${formatDate(item.endDate)}`,
                                        statusGenerate: `<button class="btn ${statusGenerateClass} btn-sm">${statusGenerateText}</button>`,
                                        actions: `<td class="text-center">
                                <a href="javascript:" onclick="generateProject(${item.kaldikID}, '${item.kaldikDesc.replace(/'/g, "\\'")}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate Project" class="btn btn-sm btn-icon btn-success mr-1">
                                    <i class="fas fa-cogs"></i>
                                </a>
                                <a href="javascript:" onclick="modalDetail(${item.kaldikID})" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Data Diklat" class="btn btn-sm btn-icon btn-primary mr-1">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="javascript:" onclick="modalPeserta(${item.kaldikID})" data-bs-toggle="tooltip" data-bs-placement="top" title="Data Peserta" class="btn btn-sm btn-icon btn-danger">
                                    <i class="fas fa-users"></i>
                                </a>
                            </td>`
                                    };
                                })
                            });
                            $('[data-bs-toggle="tooltip"]').tooltip();
                        }
                    });
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "kaldikID"
                    },
                    {
                        data: "kaldikDesc"
                    },
                    {
                        data: "diklatTypeName"
                    },
                    {
                        data: "dateRange"
                    },
                    {
                        data: "statusGenerate"
                    },
                    {
                        data: "actions"
                    }
                ]
            });

        });

        function generateProject(kaldikID, kaldikDesc) {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin membuat project untuk Diklat ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Buat!",
                cancelButtonText: "Tidak, Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('project.store') }}",
                        data: {
                            kaldikID: kaldikID,
                            kaldikDesc: kaldikDesc
                        },
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        type: "POST",
                        success: function(response) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Project berhasil dibuat!",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); // Muat ulang halaman setelah sukses
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = "Terjadi kesalahan saat membuat project.";

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: "Kesalahan!",
                                text: errorMessage,
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }



        // Modal Detail Diklat - Fetch Data with AJAX
        function modalDetail(kaldikID) {
            $.ajax({
                url: `/get-kaldik-data/detail/${kaldikID}`,
                type: "GET",
                success: function(response) {
                    var tableBody = $('#detailDiklatTable tbody');
                    tableBody.empty();
                    var data = response.data;

                    function formatDate(dateString) {
                        if (dateString) {
                            var date = new Date(dateString);
                            var year = date.getFullYear();
                            var month = ('0' + (date.getMonth() + 1)).slice(-2); // Month is 0-indexed
                            var day = ('0' + date.getDate()).slice(-2);
                            return `${year}-${month}-${day}`;
                        }
                        return '-';
                    }

                    tableBody.append(`
                        <tr>
                            <td>{{ __('Kode Diklat') }}</td>
                            <td>${data.kaldikID}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Nama Diklat') }}</td>
                            <td>${data.kaldikDesc}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Jenis Diklat') }}</td>
                            <td>${data.diklatTypeName}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Biaya') }}</td>
                            <td>${data.biayaName}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Tanggal Diklat') }}</td>
                            <td>${formatDate(data.startDate)} s/d ${formatDate(data.endDate)}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Lokasi') }}</td>
                            <td>${data.tempatName}</td>
                        </tr>
                    `);
                    if (data.tgl_mulai_el && data.tgl_selesai_el) {
                        tableBody.append(`
                        <tr>
                            <td>{{ __('Tanggal Elearning') }}</td>
                            <td>${formatDate(data.tgl_mulai_el)} s/d ${formatDate(data.tgl_selesai_el)}</td>
                        </tr>
                    `);
                    }

                    if (data.tgl_mulai_tm && data.tgl_selesai_tm) {
                        tableBody.append(`
                        <tr>
                            <td>{{ __('Tanggal Tatap Muka') }}</td>
                            <td>${formatDate(data.tgl_mulai_tm)} s/d ${formatDate(data.tgl_selesai_tm)}</td>
                        </tr>
                    `);
                    }
                    $('#modalDetailDiklat').modal('show');
                }
            });
        }


        // Modal Peserta - Fetch Peserta Data with AJAX
        function modalPeserta(kaldikID) {
    // Initialize DataTable
    var table = $('#pesertaDiklatTable').DataTable({
        processing: true,
        serverSide: true,
        destroy: true, // Ensure the table is reinitialized every time the modal is opened
        ajax: {
            url: `/get-kaldik-data/peserta/${kaldikID}`,
            type: "GET",
            data: function(d) {
                // Pass DataTable's internal parameters (pagination, search, etc.)
                d.page = (d.start / d.length) + 1; // Calculate current page
                d.limit = d.length; // Number of records per page
                d.search = d.search.value; // Current search query
            },
            dataSrc: function(response) {
                // Format the response data to DataTable's expected format
                return response.data; // Directly return the 'data' array from the response
            },
            error: function(xhr, status, error) {
                alert("Gagal mengambil data peserta!");
            }
        },
        columns: [
            { data: 'pesertaNama' },
            { data: 'pesertaNIP' },
            { data: 'pesertaTelepon' },
            { data: 'jabatanFullName' },
            { data: 'unitName' },
            { data: 'pesertaNilaiPreTest' },
            { data: 'pesertaNilaiPostTest' }
        ]
    });

    // Show the modal after the table has been initialized
    $('#modalPesertaDiklat').modal('show');
}

    </script>
@endpush
