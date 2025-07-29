@extends('layouts.app')

@section('title', __('Rekap Kuesioner'))

@section('content')
    <div class="modal fade" id="skorModal" tabindex="-1" aria-labelledby="skorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Skor Level 3</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-content">
                </div>
            </div>
        </div>
    </div>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Hasil Evaluasi Pasca Pembelajaran Level 3') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Hasil Evaluasi Pasca Pembelajaran 3') }}
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
                            <table class="table table-borderless">
                                <tr sty>
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

                            <a href="{{ route('hasil-evaluasi.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive p-1">
                                <table class="table table-striped" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">{{ __('Nama peserta') }}</th>
                                            <th rowspan="2">{{ __('NIP') }}</th>
                                            <th rowspan="2">{{ __('No.Telepon') }}</th>
                                            <th rowspan="2">{{ __('Jabatan') }}</th>
                                            <th rowspan="2">{{ __('Unit') }}</th>
                                            <th colspan="2" class="text-center">{{ __('Level 3') }}</th>
                                        </tr>

                                        <tr>
                                            <th class="text-center">Skor</th>
                                            <th class="text-center">Predikat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6" class="text-end">Rata-rata:</th>
                                            <th class="text-center" id="avg-skor-level-3">0</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/combine/npm/datatables.net@1.12.0,npm/datatables.net-bs5@1.12.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            var projectId = @json($project->id);
            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 100,
                ajax: {
                    url: "{{ route('hasil-evaluasi.detail-skor.level3', ':id') }}".replace(':id',
                        projectId),
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-start',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        className: 'text-start'
                    },
                    {
                        data: 'nip',
                        name: 'nip',
                        className: 'text-start'
                    },
                    {
                        data: 'telepon',
                        name: 'telepon',
                        className: 'text-start'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan',
                        className: 'text-start'
                    },
                    {
                        data: 'unit',
                        name: 'unit',
                        className: 'text-start'
                    },
                    {
                        data: 'avg_skor_level_3',
                        name: 'avg_skor_level_3',
                        className: "text-center skor-clickable",
                        render: function(data, type, row) {
                            return `<a href="#" class="skor-detail"  data-id="${row.project_responden_id}">${data}</a>`;
                        }
                    },
                    {
                        data: 'kriteria_dampak',
                        name: 'kriteria_dampak',
                        className: 'text-start'
                    }
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var avgSkor = api.ajax.json().average_skor_level_3; // Ambil nilai dari response
                    $("#avg-skor-level-3").text(avgSkor); // Update tampilan
                }
            });

            // Event listener untuk klik skor
            $('#data-table tbody').on('click', '.skor-detail', function(e) {
                e.preventDefault();
                var respondenId = $(this).data('id');
                $.ajax({
                    url: "{{ route('detail-level-3.responden') }}",
                    type: "GET",
                    data: {
                        project_responden_id: respondenId
                    },
                    success: function(response) {
                        $('#modal-content').html(response);
                        $('#skorModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Gagal mengambil data skor.');
                    }
                });
            });

        });
    </script>
@endpush
