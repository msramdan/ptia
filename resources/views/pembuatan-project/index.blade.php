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
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Pembuatan Project') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            {{-- Filter Jenis Diklat --}}
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="filter_jenis_diklat_pembuatan" class="form-label">Filter Jenis
                                        Diklat</label>
                                    <select class="form-select" id="filter_jenis_diklat_pembuatan">
                                        <option value="">Semua Jenis Diklat</option>
                                        @foreach ($jenisDiklatList as $jenis)
                                            <option value="{{ $jenis }}">{{ $jenis }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table-pembuatan" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>{{ __('Kode Diklat') }}</th>
                                            <th>{{ __('Nama Diklat') }}</th>
                                            <th>{{ __('Jenis Diklat') }}</th>
                                            <th>{{ __('Tanggal Diklat') }}</th>
                                            <th>{{ __('Status Generate') }}</th>
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
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Peserta Diklat -->
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
                        <tbody></tbody>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <script>
        $(document).ready(function() {
            var dataTablePembuatan; // Deklarasi di scope yang lebih luas

            function loadDataTablePembuatanProject() {
                var selectedJenisDiklat = $('#filter_jenis_diklat_pembuatan').val();

                if ($.fn.DataTable.isDataTable('#data-table-pembuatan')) {
                    dataTablePembuatan.destroy();
                }

                dataTablePembuatan = $('#data-table-pembuatan').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('kaldik.index') }}",
                        type: "GET",
                        data: function(d) {
                            d.limit = d.length;
                            d.page = (d.start / d.length) + 1;
                            d.search = d.search.value;
                            if (selectedJenisDiklat) {
                                d.jenis_diklat = selectedJenisDiklat;
                            }
                        },
                        dataSrc: function(response) {
                            return response.data.map(function(item) {
                                // Function to format date in Y-m-d format
                                function formatDate(dateString) {
                                    var date = new Date(dateString);
                                    var year = date.getFullYear();
                                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                                    var day = ('0' + date.getDate()).slice(-2);
                                    return `${year}-${month}-${day}`;
                                }

                                // Function to format duration
                                function formatDuration(endDate) {
                                    const end = moment(endDate);
                                    const now = moment();

                                    if (end.isAfter(now)) {
                                        return 'Masih berlangsung';
                                    }

                                    const duration = moment.duration(now.diff(end));
                                    const years = duration.years();
                                    const months = duration.months();
                                    const days = duration.days();

                                    let result = '';
                                    if (years > 0) result += `${years} tahun `;
                                    if (months > 0) result += `${months} bulan `;
                                    if (days > 0) result += `${days} hari `;

                                    const durationText = result.trim() + ' yang lalu';

                                    if (duration.asMonths() > 3) {
                                        return `<span style="color:red">${durationText}</span>`;
                                    }

                                    return durationText;
                                }

                                var statusGenerateClass = item.status_generate === 'SUDAH' ?
                                    'btn-success' : 'btn-danger';
                                var statusGenerateText = item.status_generate === 'SUDAH' ?
                                    'SUDAH' : 'BELUM';
                                moment.locale('id');

                                return {
                                    kaldikID: item.kaldikID,
                                    kaldikDesc: item.kaldikDesc,
                                    biayaName: item.biayaName,
                                    diklatTypeName: item.diklatTypeName,
                                    dateRange: formatDuration(item.endDate),
                                    statusGenerate: `<button class="btn ${statusGenerateClass} btn-sm">${statusGenerateText}</button>`,
                                    actions: `<td class="text-center">
                                        <a href="javascript:" onclick="generateProject(${item.kaldikID},'${item.diklatTypeName}', '${item.kaldikDesc.replace(/'/g, "\\'")}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate Project" class="btn btn-sm btn-icon btn-success mr-1">
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
                            });
                        },
                        dataFilter: function(data) {
                            var json = jQuery.parseJSON(data);
                            json.recordsTotal = json.total;
                            json.recordsFiltered = json.total;
                            return JSON.stringify(json);
                        }
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
                    ],
                    drawCallback: function(settings) {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                });
            }

            $('#filter_jenis_diklat_pembuatan').on('change', function() {
                loadDataTablePembuatanProject();
            });

            // Load tabel saat halaman pertama kali dimuat
            loadDataTablePembuatanProject();
        });

        function generateProject(kaldikID, diklatTypeName, kaldikDesc) {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin membuat project untuk Diklat ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Ya, Buat!",
                cancelButtonText: "Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const loadingSwal = Swal.fire({
                        title: "Mohon tunggu, proses sedang berlangsung...",
                        html: `
                            <div style="width: 100%; text-align: center;">
                                <div class="progress" style="width: 100%; height: 20px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        willOpen: () => {
                            $(".progress-bar").css("width", "100%");
                        },
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });

                    $.ajax({
                        url: "{{ route('project.store') }}",
                        data: {
                            kaldikID: kaldikID,
                            diklatTypeName: diklatTypeName,
                            kaldikDesc: kaldikDesc
                        },
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        type: "POST",
                        success: function(response) {
                            loadingSwal.close();
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Project berhasil dibuat!",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            loadingSwal.close();
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

        function modalDetail(kaldikID) {
            $.ajax({
                url: "{{ route('kaldik.detail', ['kaldikID' => '__ID__']) }}".replace('__ID__', kaldikID),
                type: "GET",
                success: function(response) {
                    var tableBody = $('#detailDiklatTable tbody');
                    tableBody.empty();
                    var data = response.data;

                    function formatDate(dateString) {
                        if (dateString) {
                            var date = new Date(dateString);
                            var year = date.getFullYear();
                            var month = ('0' + (date.getMonth() + 1)).slice(-2);
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

        function modalPeserta(kaldikID) {
            var table = $('#pesertaDiklatTable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('peserta.diklat', ['kaldikID' => '__ID__']) }}".replace('__ID__', kaldikID),
                    type: "GET",
                    data: function(d) {
                        d.page = (d.start / d.length) + 1;
                        d.limit = d.length;
                        d.search = d.search.value;
                    },
                    dataSrc: function(response) {
                        return response.data;
                    },
                    error: function(xhr, status, error) {
                        alert("Gagal mengambil data peserta!");
                    }
                },
                columns: [{
                        data: 'pesertaNama'
                    },
                    {
                        data: 'pesertaNIP'
                    },
                    {
                        data: 'pesertaTelepon'
                    },
                    {
                        data: 'jabatanFullName'
                    },
                    {
                        data: 'unitName'
                    },
                    {
                        data: 'pesertaNilaiPreTest'
                    },
                    {
                        data: 'pesertaNilaiPostTest'
                    }
                ]
            });

            $('#modalPesertaDiklat').modal('show');
        }
    </script>
@endpush
