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
                        {{ __('Berikut adalah daftar responden dari project.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
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
                            <form action="{{ route('project.responden.update', $kriteriaResponden->id) }}" method="POST">
                                <input type="hidden" name="project_id" id="project_id"
                                    class="form-control @error('project_id') is-invalid @enderror"
                                    value="{{ isset($kriteriaResponden) ? $kriteriaResponden->project_id : old('project_id') }}"
                                    required />
                                <input type="hidden" name="kaldikID" id="kaldikID"
                                    class="form-control @error('kaldikID') is-invalid @enderror"
                                    value="{{ isset($kriteriaResponden) ? $project->kaldikID : old('kaldikID') }}"
                                    required />

                                @csrf
                                @method('PUT')
                                <h5>Filter Responden</h5>
                                <div class="row mb-2" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                                    <div class="col-md-6 mb-2">
                                        <p>Nilai Post Test</p>
                                        @php
                                            $selectedNilaiPostTest = old(
                                                'nilai_post_test',
                                                $kriteriaResponden->nilai_post_test ?? [],
                                            );
                                        @endphp
                                        @foreach (['Turun', 'Tetap', 'Naik'] as $option)
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input @error('nilai_post_test') is-invalid @enderror"
                                                    type="checkbox" name="nilai_post_test[]" id="{{ strtolower($option) }}"
                                                    value="{{ $option }}"
                                                    {{ in_array($option, $selectedNilaiPostTest) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="{{ strtolower($option) }}">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach

                                        @error('nilai_post_test')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label
                                                for="nilai-post-test-minimal">{{ __('Nilai Post Test Minimal') }}</label>
                                            <input type="number" name="nilai_post_test_minimal"
                                                id="nilai-post-test-minimal"
                                                class="form-control @error('nilai_post_test_minimal') is-invalid @enderror"
                                                value="{{ isset($kriteriaResponden) ? $kriteriaResponden->nilai_post_test_minimal : old('nilai_post_test_minimal') }}"
                                                placeholder="{{ __('Nilai Post Test Minimal') }}" required />
                                            @error('nilai_post_test_minimal')
                                                <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('penyebaran-kuesioner.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                                </a>
                                @if ($project->status == 'Persiapan')
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                        {{ __('Submit') }}</button>
                                @endif
                            </form>

                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Daftar Responden</h5>
                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Nama peserta') }}</th>
                                            <th>{{ __('NIP') }}</th>
                                            <th>{{ __('No.Telepon') }}</th>
                                            <th>{{ __('Jabatan') }}</th>
                                            <th>{{ __('Unit') }}</th>
                                            <th>{{ __('Nilai Post Test') }}</th>
                                            <th>{{ __('Nilai Kenaikan Pre Post') }}</th>
                                            <th>{{ __('Action') }}</th>
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
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('penyebaran-kuesioner.responden-alumni.show', ':id') }}".replace(':id',
                        projectId),
                    data: function(d) {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                    },
                    {
                        data: 'nip',
                        name: 'nip',
                    },
                    {
                        data: 'telepon',
                        name: 'telepon',
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan',
                    },
                    {
                        data: 'unit',
                        name: 'unit',
                    },
                    {
                        data: 'nilai_pre_test',
                        name: 'nilai_pre_test',
                    },
                    {
                        data: 'nilai_post_test',
                        name: 'nilai_post_test',
                    },
                    {
                        data: 'action',
                        name: 'action',
                    }
                ],
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
                    let errorMessage = "Gagal mengupdate nomor telepon.";
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
                text: `Apakah Anda yakin ingin mengirim link kuesioner ke ${nama} (${telepon}) via WhatsApp?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Kirim",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('penyebaran-kuesioner.send.wa') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            remark: remark
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire("Berhasil!", response.message, "success");
                                $("#data-table").DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire("Gagal!", response.message, "error");
                            }
                        },
                        error: function() {
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
                        data: "created_at",
                        name: "created_at"
                    }
                ]
            });
        });
    </script>
@endpush
