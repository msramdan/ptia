@extends('layouts.app')

@section('title', __('Responden'))

@section('content')
    {{-- Modal update no telpon --}}
    <div class="modal fade" id="editTeleponModal" tabindex="-1" aria-labelledby="editTeleponLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTeleponLabel">Update No.Telepon - <span id="namaResponden"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="responden_id">
                    <div class="mb-3">
                        <label for="telepon" class="form-label">No.Telepon</label>
                        <input type="hidden" class="form-control" id="remark" value="Alumni" autocomplete="off">
                        <input type="text" class="form-control" id="telepon" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveTeleponBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal update deadline --}}
    <div class="modal fade" id="editDeadlineModal" tabindex="-1" aria-labelledby="editDeadlineLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDeadlineLabel">Update deadline pengisian - <span
                            id="deadlineNamaResponden"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deadlineRespondenId">
                    <div class="mb-3">
                        <label for="deadlineTanggal" class="form-label">Tanggal deadline</label>
                        <input type="hidden" class="form-control" id="remark" value="Alumni" autocomplete="off">
                        <input type="date" class="form-control" id="deadlineTanggal" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveDeadlineBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Log Pengiriman -->
    <div class="modal fade" id="logWaModal" tabindex="-1" aria-labelledby="logWaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logWaModalLabel">Log Pengiriman WhatsApp - <span
                            id="logNamaResponden"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table id="logWaTable" class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Telepon</th>
                                <th>Remark</th>
                                <th>Status</th>
                                <th>Log Pesan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Responden') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah daftar responden Alumni dari project.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('penyebaran-kuesioner.index') }}">{{ __('Penyebaran Kuesioner') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Responden') }}</li>
                </x-breadcrumb>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Kode Diklat</strong></td>
                                    <td>: {{ $project->kaldikID ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Diklat</strong></td>
                                    <td>: {{ $project->kaldikDesc ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Evaluator</strong></td>
                                    <td>: {{ $project->user_name ?? '-' }}</td>
                                </tr>
                            </table>

                            <a href="{{ route('penyebaran-kuesioner.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5>Daftar Responden Alumni</h5>

                            <!-- Button untuk Update Deadline -->
                            <button id="update-deadline-btn" class="btn mb-3"
                                style="background-color: #C0C0C0; color: black; border-color: #C0C0C0;" disabled>
                                <i class="fas fa-calendar-alt"></i> Update Deadline
                            </button>

                            <!-- Modal Bootstrap 5 -->
                            <div class="modal fade" id="updateDeadlineModal" tabindex="-1"
                                aria-labelledby="updateDeadlineModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateDeadlineModalLabel">Update Deadline</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="update-deadline-form">
                                                <div class="mb-3">
                                                    <label for="deadline-date" class="form-label">Pilih Tanggal
                                                        Deadline</label>
                                                    <input type="date" class="form-control" id="deadline-date"
                                                        name="deadline-date" required>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <button type="button" class="btn btn-primary"
                                                id="submit-deadline">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabel -->
                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="select-all"></th>
                                            <th>#</th>
                                            <th>{{ __('Nama peserta') }}</th>
                                            <th>{{ __('NIP') }}</th>
                                            <th>{{ __('No.Telepon') }}</th>
                                            <th>{{ __('Jabatan') }}</th>
                                            <th>{{ __('Unit') }}</th>
                                            <th>{{ __('Nilai Pre Test') }}</th>
                                            <th>{{ __('Nilai Post Post') }}</th>
                                            <th>{{ __('Deadline') }}</th>
                                            <th>{{ __('Aksi') }}</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            var selectedIds = []; // Array untuk menyimpan ID yang dipilih
            var isSelectAll = false; // Status untuk menandai apakah "Select All" aktif

            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true, // Menyimpan state tabel, termasuk pagination
                pageLength: 10, // Default per page 10
                ajax: {
                    url: "{{ route('penyebaran-kuesioner.responden-alumni.show', ':id') }}".replace(':id',
                        projectId),
                    data: function(d) {}
                },
                columns: [{
                        data: 'id', // Asumsi Anda memiliki kolom ID
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Cek apakah ID sudah dipilih
                            var isChecked = selectedIds.includes(data
                                .toString()); // Pastikan ID diubah ke string
                            return '<input type="checkbox" class="select-checkbox" value="' + data +
                                '" ' + (isChecked ? 'checked' : '') + '>';
                        }
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'nip',
                        name: 'nip'
                    },
                    {
                        data: 'telepon',
                        name: 'telepon',
                        className: 'text-center'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'nilai_pre_test',
                        name: 'nilai_pre_test'
                    },
                    {
                        data: 'nilai_post_test',
                        name: 'nilai_post_test'
                    },
                    {
                        data: 'deadline_pengisian_alumni',
                        name: 'deadline_pengisian_alumni'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ],
                drawCallback: function(settings) {
                    // Pulihkan state checkbox saat pindah halaman
                    $('.select-checkbox').each(function() {
                        var id = $(this).val();
                        if (selectedIds.includes(id
                                .toString())) { // Pastikan ID diubah ke string
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });

                    // Update status "Select All"
                    updateSelectAllStatus();
                    // Update status button "Update Deadline"
                    updateUpdateDeadlineButton();
                }
            });

            // Fungsi untuk mengupdate status "Select All"
            function updateSelectAllStatus() {
                // Ambil semua data dari server untuk memeriksa apakah semua data terpilih
                $.ajax({
                    url: "{{ route('penyebaran-kuesioner.responden-alumni.show', ':id') }}".replace(':id',
                        projectId),
                    data: {
                        length: -1
                    }, // Request semua data
                    success: function(response) {
                        var allDataIds = response.data.map(function(item) {
                            return item.id.toString(); // Pastikan ID diubah ke string
                        });

                        // Cek apakah semua data terpilih
                        var allSelected = allDataIds.every(function(id) {
                            return selectedIds.includes(id);
                        });

                        // Update status "Select All"
                        $('#select-all').prop('checked', allSelected);
                        isSelectAll = allSelected;
                    }
                });
            }

            // Event listener untuk "select all"
            $('#select-all').on('click', function() {
                isSelectAll = this.checked;

                if (isSelectAll) {
                    // Ambil semua data dari server (tanpa pagination)
                    $.ajax({
                        url: "{{ route('penyebaran-kuesioner.responden-alumni.show', ':id') }}"
                            .replace(':id', projectId),
                        data: {
                            length: -1
                        }, // Request semua data
                        success: function(response) {
                            selectedIds = response.data.map(function(item) {
                                return item.id
                                    .toString(); // Pastikan ID diubah ke string
                            });

                            // Centang semua checkbox
                            $('.select-checkbox').prop('checked', true);
                            table.draw(false); // Redraw tabel tanpa reset pagination
                            // Update status button "Update Deadline"
                            updateUpdateDeadlineButton();
                        }
                    });
                } else {
                    // Kosongkan selectedIds dan uncheck semua checkbox
                    selectedIds = [];
                    $('.select-checkbox').prop('checked', false);
                    table.draw(false); // Redraw tabel tanpa reset pagination
                    // Update status button "Update Deadline"
                    updateUpdateDeadlineButton();
                }
            });

            // Event listener untuk checkbox individual
            $('#data-table tbody').on('change', '.select-checkbox', function() {
                var id = $(this).val();

                if ($(this).is(':checked')) {
                    if (!selectedIds.includes(id.toString())) { // Pastikan ID diubah ke string
                        selectedIds.push(id.toString());
                    }
                } else {
                    selectedIds = selectedIds.filter(function(item) {
                        return item !== id.toString(); // Pastikan ID diubah ke string
                    });
                }

                // Update status "Select All"
                updateSelectAllStatus();
                // Update status button "Update Deadline"
                updateUpdateDeadlineButton();
            });

            // Fungsi untuk mengupdate status button "Update Deadline"
            function updateUpdateDeadlineButton() {
                if (selectedIds.length > 0) {
                    $('#update-deadline-btn').prop('disabled', false);
                } else {
                    $('#update-deadline-btn').prop('disabled', true);
                }
            }

            // Event listener untuk button "Update Deadline"
            $('#update-deadline-btn').on('click', function() {
                $('#updateDeadlineModal').modal('show');
            });

            // Event listener untuk tombol submit di modal
            $('#submit-deadline').on('click', function() {
                var deadline = $('#deadline-date').val(); // Ambil nilai tanggal

                if (!deadline) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Harap pilih tanggal deadline.',
                    });
                    return;
                }

                // Kirim data ke server
                $.ajax({
                    url: "{{ route('update-deadline') }}", // Route untuk update deadline
                    method: 'POST',
                    data: {
                        ids: selectedIds,
                        deadline: deadline,
                        remark: 'Alumni', // Ganti dengan 'Atasan' jika diperlukan
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status) {
                            // Jika berhasil, tampilkan SweetAlert sukses
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                            }).then(() => {
                                $('#updateDeadlineModal').modal(
                                    'hide'); // Sembunyikan modal
                                $('#deadline-date').val(''); // Reset input tanggal
                                table.draw(false); // Refresh tabel
                            });
                        } else {
                            // Jika gagal, tampilkan SweetAlert error
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        // Jika terjadi error pada AJAX, tampilkan SweetAlert error
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan. Silakan coba lagi.',
                        });
                    }
                });
            });
        });
    </script>

    <script>
        $(document).on("click", ".edit-telepon-btn", function() {
            let id = $(this).data("id");
            let telepon = $(this).data("telepon");
            let nama = $(this).data("nama");

            $("#responden_id").val(id);
            $("#telepon").val(telepon);
            $("#namaResponden").text(nama);
            $("#editTeleponModal").modal("show");
        });


        $("#saveTeleponBtn").on("click", function() {
            let id = $("#responden_id").val();
            let telepon = $("#telepon").val();
            let remark = $("#remark").val();

            $.ajax({
                url: "{{ route('penyebaran-kuesioner.update.telepon') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    telepon: telepon,
                    remark: remark,
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $("#editTeleponModal").modal("hide");
                            $("#data-table").DataTable().ajax.reload(null,
                                false); // 🔄 Refresh DataTables tanpa reload halaman
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Terjadi kesalahan!",
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Gagal mengupdate nomor telepon alumni.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    Swal.fire({
                        icon: "error",
                        title: "Validasi Gagal!",
                        text: errorMessage,
                    });
                },
            });
        });
    </script>

    <script>
        $(document).on("click", ".edit-deadline-btn", function() {
            let idResponden = $(this).data("id");
            let deadlineTanggal = $(this).data("deadline");
            let namaResponden = $(this).data("nama");

            $("#deadlineRespondenId").val(idResponden);
            $("#deadlineTanggal").val(deadlineTanggal);
            $("#deadlineNamaResponden").text(namaResponden);

            $("#editDeadlineModal").modal("show");
        });

        $("#saveDeadlineBtn").on("click", function() {
            let id = $("#deadlineRespondenId").val();
            let deadline = $("#deadlineTanggal").val();
            let remark = $("#remark").val();

            $.ajax({
                url: "{{ route('penyebaran-kuesioner.update.deadline') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    deadline: deadline,
                    remark: remark,
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $("#editDeadlineModal").modal("hide");
                            $("#data-table").DataTable().ajax.reload(null, false);
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Terjadi kesalahan!",
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Gagal mengupdate deadline.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    Swal.fire({
                        icon: "error",
                        title: "Validasi Gagal!",
                        text: errorMessage,
                    });
                },
            });
        });
    </script>

    <script>
        $(document).on("click", ".send-wa-btn", function() {
            let id = $(this).data("id");
            let remark = $(this).data("remark");
            let nama = $(this).data("nama");
            let telepon = $(this).data("telepon");

            Swal.fire({
                title: "Kirim Notifikasi?",
                text: `Apakah Anda yakin ingin mengirim link kuesioner ke Alumni ${nama} (${telepon}) via WhatsApp?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Kirim",
                cancelButtonText: "Batal",
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
                        url: "{{ route('penyebaran-kuesioner.send.wa') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            remark: remark
                        },
                        success: function(response) {
                            loadingSwal.close();
                            if (response.success) {
                                Swal.fire("Berhasil!", response.message, "success");
                                $("#data-table").DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire("Gagal!", response.message, "error");
                            }
                        },
                        error: function() {
                            loadingSwal.close();
                            Swal.fire("Gagal!", "Terjadi kesalahan saat mengirim notifikasi.",
                                "error");
                        },
                    });
                }
            });
        });
    </script>

    {{-- Log pengiriman pesan wa --}}
    <script>
        $(document).on("click", ".log-wa-btn", function() {
            let id = $(this).data("id");
            let remark = $(this).data("remark");
            let nama = $(this).data("nama"); // Ambil nama responden

            $("#logNamaResponden").text(nama); // Tampilkan nama di modal
            $("#logWaModal").modal("show");

            // Hancurkan DataTables jika sudah ada, agar tidak ada duplikasi data lama
            if ($.fn.DataTable.isDataTable("#logWaTable")) {
                $("#logWaTable").DataTable().destroy();
                $("#logWaTable tbody").empty();
            }

            // Inisialisasi ulang DataTables
            $("#logWaTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('penyebaran-kuesioner.log.wa') }}",
                    type: "GET",
                    data: {
                        id: id,
                        remark: remark
                    }
                },
                columns: [{
                        data: "telepon",
                        name: "telepon"
                    },
                    {
                        data: "remark",
                        name: "remark"
                    },
                    {
                        data: "status",
                        name: "status",
                        render: function(data) {
                            if (data === "Sukses") {
                                return '<span class="badge bg-success">Sukses</span>';
                            } else {
                                return '<span class="badge bg-danger">Gagal</span>';
                            }
                        }
                    },
                    {
                        data: "log_pesan",
                        name: "log_pesan"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    }
                ]
            });
        });
    </script>
@endpush
