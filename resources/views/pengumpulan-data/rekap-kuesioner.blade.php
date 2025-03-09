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
                            <a href="{{ route('pengumpulan-data.export-excel', ['id' => $project->id, 'remark' => $remark]) }}"
                                class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export ke Excel
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5>Data Rekap Kuesioner</h5>
                            <div class="table-responsive p-1">
                                <table class="table table-bordered table-striped text-center align-middle" id="data-table"
                                    width="100%" style="font-size: 12px">
                                    <thead class="table-primary">
                                        <tr>
                                            <th rowspan="3">No.</th>
                                            <th rowspan="3">Nama Peserta</th>
                                            <th rowspan="3">NIP</th>
                                            @if ($remark == 'Atasan')
                                                <th rowspan="3">Nama Atlas</th>
                                            @endif
                                            @php
                                                $groupedLevels = collect($kuesioner)->groupBy('level');
                                            @endphp

                                            @foreach ($groupedLevels as $level => $aspeks)
                                                <th colspan="{{ count($aspeks) * 4 }}" class="bg-info text-white">
                                                    LEVEL {{ $level }}
                                                </th>
                                                <th rowspan="3" class="bg-secondary text-white">Total LEVEL
                                                    {{ $level }} (Data Primer)</th>
                                            @endforeach
                                            <th rowspan="3">Aksi</th>
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
                                                    <th style="width: 90px">Bobot</th>
                                                    <th>Nilai</th>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($responden as $index => $respondenItem)
                                            @php
                                                $sql = "WITH delta_data AS (
                                            SELECT
                                                project_jawaban_kuesioner.project_responden_id,
                                                project_kuesioner.aspek_id,
                                                project_kuesioner.aspek,
                                                project_kuesioner.kriteria,
                                                project.id AS project_id,
                                                project.diklat_type_id,
                                                project_jawaban_kuesioner.remark,
                                                COUNT(project_jawaban_kuesioner.id) AS jumlah_data,
                                                SUM(project_jawaban_kuesioner.nilai_sesudah - project_jawaban_kuesioner.nilai_sebelum) AS total_delta,
                                                ROUND(AVG(project_jawaban_kuesioner.nilai_sesudah - project_jawaban_kuesioner.nilai_sebelum)) AS rata_rata_delta
                                            FROM project_jawaban_kuesioner
                                            JOIN project_kuesioner ON project_jawaban_kuesioner.project_kuesioner_id = project_kuesioner.id
                                            JOIN project ON project_kuesioner.project_id = project.id
                                            WHERE project_jawaban_kuesioner.project_responden_id = :responden_id
                                                AND project_jawaban_kuesioner.remark = :remark
                                            GROUP BY
                                                project_jawaban_kuesioner.project_responden_id,
                                                project_kuesioner.aspek_id,
                                                project_kuesioner.aspek,
                                                project_kuesioner.kriteria,
                                                project.diklat_type_id,
                                                project_jawaban_kuesioner.remark
                                        )
                                        SELECT
                                            delta_data.*,
                                            COALESCE(konversi.konversi, 0) AS konversi_nilai,
                                            COALESCE(
                                                CASE
                                                    WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                                                    WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                                                    ELSE 0
                                                END, 0
                                            ) AS bobot,
                                            -- Perhitungan nilai
                                            ROUND((COALESCE(konversi.konversi, 0) *
                                                   COALESCE(
                                                       CASE
                                                           WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                                                           WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                                                           ELSE 0
                                                       END, 0
                                                   ) / 100), 2) AS nilai
                                        FROM delta_data
                                        LEFT JOIN konversi
                                            ON delta_data.diklat_type_id = konversi.diklat_type_id
                                            AND delta_data.rata_rata_delta = konversi.skor
                                            AND (
                                                (delta_data.kriteria = 'Skor Persepsi' AND konversi.jenis_skor = 'Skor Persepsi')
OR
                                                (delta_data.kriteria = 'Delta Skor Persepsi' AND konversi.jenis_skor = '∆ Skor Persepsi')
                                            )
                                        LEFT JOIN project_bobot_aspek
                                            ON delta_data.project_id = project_bobot_aspek.project_id
                                            AND delta_data.aspek_id = project_bobot_aspek.aspek_id
                                            AND delta_data.aspek = project_bobot_aspek.aspek;";

                                                $result = DB::select($sql, [
                                                    'responden_id' => $respondenItem->id,
                                                    'remark' => $remark,
                                                ]);
                                            @endphp

                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $respondenItem->nama }}</td>
                                                <td>{{ $respondenItem->nip }}</td>
                                                @if ($remark == 'Atasan')
                                                    <td>{{ $respondenItem->nama_atasan }}</td>
                                                @endif
                                                @php
                                                    $total_level_3 = 0;
                                                @endphp
                                                @foreach ($groupedLevels[3] as $aspek)
                                                    @php
                                                        $data = collect($result)->firstWhere('aspek', $aspek->aspek);
                                                        $nilai = $data->nilai ?? 0;
                                                        $total_level_3 += $nilai;
                                                    @endphp
                                                    <td>{{ $data->rata_rata_delta ?? '-' }}</td>
                                                    <td>{{ $data->konversi_nilai ?? '-' }}</td>
                                                    <td>{{ $data->bobot ?? '-' }}%</td>
                                                    <td style="color: green">
                                                        <b
                                                            title="Perhitungan: ({{ $data->konversi_nilai ?? 0 }} * {{ $data->bobot ?? 0 }}%)">
                                                            {{ $nilai }}
                                                        </b>
                                                    </td>
                                                @endforeach

                                                {{-- Total Level 3 --}}
                                                <td style="color: red">
                                                    <b
                                                        title="Total level 3: {{ implode(' + ', collect($groupedLevels[3])->map(fn($aspek) => collect($result)->firstWhere('aspek', $aspek->aspek)->nilai ?? 0)->toArray()) }}">
                                                        {{ $total_level_3 }}
                                                    </b>
                                                </td>


                                                {{-- Looping Level 4 --}}
                                                @php
                                                    $total_level_4 = 0;
                                                @endphp
                                                @foreach ($groupedLevels[4] as $aspek)
                                                    @php
                                                        $data = collect($result)->firstWhere('aspek', $aspek->aspek);
                                                        $nilai = $data->nilai ?? 0;
                                                        $total_level_4 += $nilai;
                                                    @endphp
                                                    <td>{{ $data->rata_rata_delta ?? '-' }}</td>
                                                    <td>{{ $data->konversi_nilai ?? '-' }}</td>
                                                    <td>{{ $data->bobot ?? '-' }}%</td>
                                                    <td style="color: green">
                                                        <b
                                                            title="Perhitungan: ({{ $data->konversi_nilai ?? 0 }} * {{ $data->bobot ?? 0 }}%)">
                                                            {{ $nilai }}
                                                        </b>
                                                    </td>
                                                @endforeach
                                                <td style="color: red">
                                                    <b
                                                        title="Total level 4: {{ implode(' + ', collect($groupedLevels[4])->map(fn($aspek) => collect($result)->firstWhere('aspek', $aspek->aspek)->nilai ?? 0)->toArray()) }}">
                                                        {{ $total_level_4 }}
                                                    </b>
                                                </td>

                                                <td>
                                                    <a href="{{ route('responden-kuesioner.index', [
                                                        'id' => encryptShort($respondenItem->id),
                                                        'target' => encryptShort($remark),
                                                        'token' => $respondenItem->token,
                                                    ]) }}"
                                                        class="btn btn-success btn-sm" target="_blank"
                                                        title="Lihat Kuesioner">
                                                        <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                                                        <span class="visually-hidden">Lihat Kuesioner</span>
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
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
@endpush
