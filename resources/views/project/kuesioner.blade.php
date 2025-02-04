@extends('layouts.app')

@section('title', __('Kuesioner'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Kuesioner') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah daftar kuesioner dari project.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('project.index') }}">{{ __('Management Project') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Kuesioner') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kuesioner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="editId" name="id">
                            <div class="mb-3">
                                <label for="editKriteria" class="form-label">Kriteria</label>
                                <select class="form-control" id="editAspek" name="aspek" required>
                                    <option value="">-- Pilih Aspek --</option>
                                    @foreach ($aspeks as $aspek)
                                        <option value="{{ $aspek->id }}">{{ $aspek->aspek }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="editKriteria" class="form-label">Kriteria</label>
                                <select class="form-control" id="editKriteria" name="kriteria" required>
                                    <option value="Delta Skor Persepsi">Delta Skor Persepsi</option>
                                    <option value="Skor Persepsi">Skor Persepsi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="editPertanyaan" class="form-label">Pertanyaan</label>
                                <textarea class="form-control" id="editPertanyaan" name="pertanyaan" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Kuesioner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addForm" method="POST" action="{{ route('kuesioner.store') }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="remark" value="{{$remark}}" readonly>
                            <input type="hidden" name="project_id" value="{{$project->id}}" readonly>
                            <div class="mb-3">
                                <label for="aspek" class="form-label">Aspek</label>
                                <select class="form-control" id="aspek" name="aspek" required>
                                    <option value="">-- Pilih Aspek --</option>
                                    @foreach ($aspeks as $aspek)
                                        <option value="{{ $aspek->id }}">{{ $aspek->aspek }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="kriteria" class="form-label">Kriteria</label>
                                <select class="form-control" id="kriteria" name="kriteria" required>
                                    <option value="Delta Skor Persepsi">Delta Skor Persepsi</option>
                                    <option value="Skor Persepsi">Skor Persepsi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="pertanyaan" class="form-label">Pertanyaan</label>
                                <textarea class="form-control" id="pertanyaan" name="pertanyaan" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <section class="section">
            <div class="row">
                <!-- Card Pertama: Informasi Proyek -->
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
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $project->user_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Target Responden</strong></td>
                                    <td>: {{ ucfirst($remark) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <a href="{{ route('project.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                                </a>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fas fa-plus"></i> {{ __('Tambah data') }}
                                </button>
                            </div>

                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Aspek') }}</th>
                                            <th>{{ __('Kriteria') }}</th>
                                            <th>{{ __('Pertanyaan') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kuesioners as $index => $kuesioner)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $kuesioner->aspek_nama ?? '-' }}</td>
                                                <td>{{ $kuesioner->kriteria ?? '-' }}</td>
                                                <td>{{ $kuesioner->pertanyaan ?? '-' }}</td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button onclick="openEditModal({{ $kuesioner->id }})"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteKuesioner({{ $kuesioner->id }})"
                                                            class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if ($kuesioners->isEmpty())
                                    <p class="text-center text-muted mt-3">Tidak ada data kuesioner.</p>
                                @endif
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openEditModal(id) {
            $.ajax({
                url: `/project/kuesioner/edit/${id}`,
                type: "GET",
                success: function(data) {
                    $('#editId').val(data.id);
                    $('#editAspek').val(data.aspek_id).change();
                    $('#editKriteria').val(data.kriteria).change();
                    $('#editPertanyaan').val(data.pertanyaan);
                    $('#editForm').attr('action', `/project/kuesioner/update/${id}`);
                    $('#editModal').modal('show');
                },
                error: function() {
                    Swal.fire("Error!", "Failed to fetch data, please try again.", "error");
                }
            });
        }


        function deleteKuesioner(id) {
            Swal.fire({
                title: "Delete Kuesioner?",
                text: "Deleted data cannot be recovered!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Delete!",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/project/kuesioner/delete/${id}`,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.fire("Deleted!", "The kuesioner has been deleted.", "success").then(
                                () => {
                                    location.reload();
                                });
                        },
                        error: function() {
                            Swal.fire("Error!", "An error occurred, please try again.", "error");
                        }
                    });
                }
            });
        }
    </script>
@endpush
