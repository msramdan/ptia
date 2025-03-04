@extends('layouts.app')

@section('title', __('Rekap Kuesioner'))

@section('content')

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Rekap Kuesioner') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah rekap hasil kuesioner.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('pengumpulan-data.index') }}">{{ __('Pengumpulan Data') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Rekap Kuesioner') }}</li>
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
                                <tr>
                                    <td><strong>Target</strong></td>
                                    <td>: {{ $remark }}</td>
                                </tr>
                            </table>

                            <a href="{{ route('pengumpulan-data.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                            </a>
                            <a href="#" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> {{ __('Export Data') }}
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5>Data Rekap Kuesioner</h5>
                            <div class="table-responsive p-1">
                                <table class="table table-bordered table-striped text-center align-middle" id="data-table" width="100%">
                                    <thead class="table-primary">
                                        <tr>
                                            <th rowspan="3">No.</th>
                                            <th rowspan="3">Nama</th>
                                            <th rowspan="3">NIP</th>

                                            @php
                                                // Kelompokkan data berdasarkan level
                                                $groupedLevels = collect($kuesioner)->groupBy('level');
                                            @endphp

                                            @foreach ($groupedLevels as $level => $aspeks)
                                                <th colspan="{{ count($aspeks) * 4 }}" class="bg-info text-white">
                                                    LEVEL {{ $level }}
                                                </th>
                                                <th rowspan="3" class="bg-secondary text-white">Total LEVEL {{ $level }} (Data Primer)</th>
                                            @endforeach

                                            <th rowspan="3" class="bg-danger text-white">Aksi</th>
                                        </tr>
                                        <tr>
                                            @foreach ($groupedLevels as $level => $aspeks)
                                                @foreach ($aspeks as $aspek)
                                                    <th colspan="4" class="bg-light">{{ $aspek->aspek }}</th>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($groupedLevels as $level => $aspeks)
                                                @foreach ($aspeks as $aspek)
                                                    <th>Skor</th>
                                                    <th>Konversi</th>
                                                    <th>Bobot</th>
                                                    <th>Nilai</th>
                                                @endforeach
                                            @endforeach
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
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            var projectId = @json($project->id);
            var remark = @json($remark);

            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('penyebaran-kuesioner.rekap.kuesioner', [':id', ':remark']) }}"
                        .replace(':id', projectId)
                        .replace(':remark', remark),
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
                    }
                ],
            });
        });
    </script>
@endpush
