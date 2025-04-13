@extends('layouts.app')

@section('title', __('Data Interview Atasan'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Data Interview Atasan Langsung') }}</h3>
                    <p class="text-subtitle text-muted">
                        Project: {{ $project->kaldikDesc ?? $project->kaldikID }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('data-interview.index') }}">{{ __('Data Interview') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Responden Atasan') }}</li>
                </x-breadcrumb>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td style="width: 15%;"><strong>Kode Diklat</strong></td>
                                    <td>: {{ $project->kaldikID ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Diklat</strong></td>
                                    <td>: {{ $project->kaldikDesc ?? '-' }}</td>
                                </tr>
                            </table>
                            <a href="{{ route('data-interview.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Kembali') }}
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Daftar Responden Atasan Langsung</h4>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="table-responsive p-1">
                                <table class="table table-striped table-bordered" id="responden-atasan-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Nama Peserta (Alumni)</th>
                                            <th>NIP Peserta</th>
                                            <th>Nama Atasan</th>
                                            <th>Telepon Atasan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="uploadEvidenceModal" tabindex="-1" aria-labelledby="uploadEvidenceLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg"> {{-- Ukuran modal diperbesar --}}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadEvidenceLabel">Upload Evidence Interview Atasan - <span
                                id="respondenNama"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadEvidenceForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="respondenId">
                            <div class="mb-3">
                                <label for="hasilInterviewAtasanText" class="form-label">Catatan Hasil Interview</label>
                                <textarea name="hasil_interview_atasan_text" id="hasilInterviewAtasanText" class="form-control"></textarea>
                                <div class="invalid-feedback" id="hasilInterviewAtasanTextError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="evidenceAtasanFile" class="form-label">File Evidence (Opsional)</label>
                                <input type="file" name="evidence_atasan_file" id="evidenceAtasanFile"
                                    class="form-control" accept=".doc,.docx,.pdf,.xls,.xlsx,.jpg,.jpeg,.png"
                                    title="Pilih file evidence (doc, docx, pdf, xls, xlsx, jpg, jpeg, png)">
                                <div class="invalid-feedback" id="evidenceAtasanFileError"></div>
                            </div>
                            <div class="mb-3" id="currentEvidence" style="display: none;">
                                <label class="form-label">Evidence Saat Ini</label>
                                <div>
                                    <a href="#" id="currentEvidenceLink" target="_blank" class="text-primary"></a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="saveEvidenceBtn">Simpan</button>
                    </div>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .table th,
        .table td {
            vertical-align: middle;
            font-size: 0.9em;
        }

        .invalid-feedback {
            font-size: 0.8em;
        }

        .ck-editor__editable_inline {
            min-height: 150px;
        }

        .modal-lg .modal-body {
            overflow-y: auto;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

    <script>
        let atasanEditor;

        ClassicEditor
            .create(document.querySelector('#hasilInterviewAtasanText'))
            .then(editor => {
                atasanEditor = editor;
                console.log('CKEditor Atasan Ready');
            })
            .catch(error => {
                console.error('Error initializing CKEditor Atasan:', error);
            });

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

            var table = $('#responden-atasan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('data-interview.responden.atasan', ['project' => $project->id]) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
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
                        data: 'nama_atasan',
                        name: 'nama_atasan'
                    },
                    {
                        data: 'telepon_atasan',
                        name: 'telepon_atasan'
                    },
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            // Escape data
                            var escapedNama = $('<div>').text(row.nama || '').html(); // Nama Alumni
                            var escapedNamaAtasan = $('<div>').text(row.nama_atasan || '')
                                .html(); // Nama Atasan (untuk modal)
                            var escapedHasil = $('<div>').text(row.hasil_intervie_atasan || '')
                                .html();
                            var evidenceFileName = row.evidence_intervie_atasan ||
                                '';
                            var hasilInterview = row.hasil_intervie_atasan || '';

                            var buttonText = hasilInterview ? 'Ganti Evidence' :
                            'Upload Evidence';
                            var buttonClass = hasilInterview ? 'btn-success' : 'btn-primary';
                            var buttonTitle = hasilInterview ? 'Ganti Evidence' :
                                'Upload Evidence';
                            // Bangun HTML tombol
                            var html =
                                '<button class="btn btn-sm ' + buttonClass +
                                ' btn-upload-evidence"' +
                                ' data-id="' + row.id + '"' +
                                // PERBAIKAN: Gunakan nama atasan untuk data-nama agar modal benar
                                ' data-nama="' + escapedNamaAtasan + '"' +
                                ' data-hasil="' + escapedHasil + '"' +
                                ' data-evidence="' + evidenceFileName + '"' +
                                ' title="' + buttonTitle + '">' +
                                '<i class="fas fa-upload"></i> ' + buttonText +
                                '</button>';

                            // Hapus atau komentari console.log jika sudah tidak diperlukan
                            // console.log('Rendered HTML for action column:', html);
                            return html;
                        }
                    }
                ]
            });

            // Buka modal saat tombol aksi diklik
            $('#responden-atasan-table').on('click', '.btn-upload-evidence', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var hasil = $(this).data('hasil');
                var evidence = $(this).data('evidence');

                $('#respondenId').val(id);
                $('#respondenNama').text(nama);

                if (atasanEditor) {
                    atasanEditor.setData(hasil || '');
                } else {
                    $('#hasilInterviewAtasanText').val(hasil);
                }

                $('#hasilInterviewAtasanText').val(hasil);
                $('#evidenceAtasanFile').val('');

                if (evidence) {
                    $('#currentEvidenceLink').attr('href',
                        '{{ asset('storage/uploads/data-interview-atasan') }}/' + evidence).text(
                        evidence);
                    $('#currentEvidence').show();
                } else {
                    $('#currentEvidence').hide();
                }

                $('#hasilInterviewAtasanTextError').text('');
                $('#evidenceAtasanFileError').text('');

                $('#uploadEvidenceModal').modal('show');
            });

            // Submit formulir modal dengan AJAX
            $('#saveEvidenceBtn').on('click', function() {
                if (atasanEditor) {
                    const editorData = atasanEditor.getData();
                    $('#hasilInterviewAtasanText').val(editorData);
                }

                var form = $('#uploadEvidenceForm');
                var formData = new FormData(form[0]);
                var respondenId = $('#respondenId').val();
                $.ajax({
                    url: "{{ url('data-interview/responden') }}/" + respondenId +
                        "/atasan-evidence",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message, 'Success');
                        $('#uploadEvidenceModal').modal('hide');
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors || {};
                        $('#hasilInterviewAtasanTextError').text('');
                        $('#evidenceAtasanFileError').text('');

                        if (errors.hasil_interview_atasan_text) {
                            $('#hasilInterviewAtasanTextError').text(errors
                                .hasil_interview_atasan_text[0]);
                        }
                        if (errors.evidence_atasan_file) {
                            $('#evidenceAtasanFileError').text(errors.evidence_atasan_file[0]);
                        }

                        Object.keys(errors).forEach(function(key) {
                            toastr.error(errors[key][0], 'Error');
                        });
                    }
                });
            });
        });
    </script>
@endpush
