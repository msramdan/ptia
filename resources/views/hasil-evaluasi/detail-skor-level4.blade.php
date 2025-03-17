@extends('layouts.app')

@section('title', __('Rekap Kuesioner'))

@section('content')

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Hasil Evaluasi Pasca Pembelajaran Level 4') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Hasil Evaluasi Pasca Pembelajaran Level 4') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
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
                            </table>

                            <a href="/hasil-evaluasi" class="btn btn-secondary">
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
                                            <th rowspan="2">#</th>
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
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            var projectId = @json($project->id);
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('hasil-evaluasi.detail-skor.level4', ':id') }}".replace(':id',
                        projectId),
                    data: function(d) {}
                },
                columns: [{
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
                        name: 'telepon'
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
                        data: 'avg_skor_level_4',
                        name: 'avg_skor_level_4'
                    },
                    {
                        data: 'kriteria_dampak',
                        name: 'kriteria_dampak'
                    }
                ]
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function calculateAverage() {
                let totalSkor = 0;
                let count = 0;

                document.querySelectorAll("#data-table tbody tr").forEach(row => {
                    let skorCell = row.cells[6]; // Kolom skor Level 3 (indeks ke-6)
                    let skorValue = parseFloat(skorCell.textContent) || 0;

                    totalSkor += skorValue;
                    count++;
                });

                let avgSkor = count > 0 ? (totalSkor / count).toFixed(2) : 0;
                document.getElementById("avg-skor-level-3").textContent = avgSkor;
            }

            // Panggil fungsi setelah tabel dimuat
            setTimeout(calculateAverage, 1000);
        });
    </script>
@endpush
