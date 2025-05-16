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
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
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
                                    <td><strong>Evaluator</strong></td>
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
                                                {{-- MODIFIKASI: colspan diubah dari count * 4 menjadi count * 5 --}}
                                                <th colspan="{{ count($aspeks) * 5 }}" class="bg-secondary text-white">
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
                                                    {{-- MODIFIKASI: colspan diubah dari 4 menjadi 5 --}}
                                                    <th colspan="5" class="bg-light">{{ $aspek->aspek }}</th>
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
                                                    {{-- PENAMBAHAN: Header kolom Catatan --}}
                                                    <th>Catatan</th>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($responden as $index => $respondenItem)
                                            @php
                                                // Query SQL yang sama dengan file export
                                                $sql = "WITH delta_data AS (
                                                            SELECT
                                                                pjk.project_responden_id,
                                                                pk.aspek_id,
                                                                pk.aspek,
                                                                pk.kriteria,
                                                                p.id AS project_id,
                                                                p.diklat_type_id,
                                                                pjk.remark,
                                                                COUNT(pjk.id) AS jumlah_data,
                                                                SUM(pjk.nilai_sesudah - pjk.nilai_sebelum) AS total_delta,
                                                                ROUND(AVG(pjk.nilai_sesudah - pjk.nilai_sebelum)) AS rata_rata_delta
                                                            FROM project_jawaban_kuesioner pjk
                                                            JOIN project_kuesioner pk ON pjk.project_kuesioner_id = pk.id
                                                            JOIN project p ON pk.project_id = p.id
                                                            WHERE pjk.project_responden_id = :responden_id_sql_param
                                                                AND pjk.remark = :remark_sql_param
                                                            GROUP BY
                                                                pjk.project_responden_id,
                                                                pk.aspek_id,
                                                                pk.aspek,
                                                                pk.kriteria,
                                                                p.id,
                                                                p.diklat_type_id,
                                                                pjk.remark
                                                        )
                                                        SELECT
                                                            dd.*,
                                                            (SELECT GROUP_CONCAT(pjk_inner.catatan SEPARATOR '; ')
                                                                FROM project_jawaban_kuesioner pjk_inner
                                                                JOIN project_kuesioner pk_inner_q ON pjk_inner.project_kuesioner_id = pk_inner_q.id
                                                                WHERE pjk_inner.project_responden_id = dd.project_responden_id
                                                                AND pk_inner_q.aspek_id = dd.aspek_id
                                                                AND pjk_inner.remark = dd.remark
                                                                AND pjk_inner.catatan IS NOT NULL AND pjk_inner.catatan != '')
AS catatan_aspek,
                                                            COALESCE(k.konversi, 0) AS konversi_nilai,
                                                            COALESCE(
                                                                CASE
                                                                    WHEN dd.remark = 'Alumni' THEN pba.bobot_alumni
                                                                    WHEN dd.remark = 'Atasan' THEN pba.bobot_atasan_langsung
                                                                    ELSE 0
                                                                END, 0
                                                            ) AS bobot,
                                                            ROUND((COALESCE(k.konversi, 0) *
                                                                   COALESCE(
                                                                       CASE
                                                                           WHEN dd.remark = 'Alumni' THEN pba.bobot_alumni
                                                                           WHEN dd.remark = 'Atasan' THEN pba.bobot_atasan_langsung
                                                                           ELSE 0
                                                                       END, 0
                                                                   ) / 100), 2) AS nilai
                                                        FROM delta_data dd
                                                        LEFT JOIN konversi k
                                                            ON dd.diklat_type_id = k.diklat_type_id
                                                            AND dd.rata_rata_delta = k.skor
                                                            AND (
                                                                (dd.kriteria = 'Skor Persepsi' AND k.jenis_skor = 'Skor Persepsi')
                                                                OR
                                                                (dd.kriteria = 'Delta Skor Persepsi' AND k.jenis_skor = '∆ Skor Persepsi')
                                                            )
                                                        LEFT JOIN project_bobot_aspek pba
                                                            ON dd.project_id = pba.project_id
                                                            AND dd.aspek_id = pba.aspek_id
                                                            AND dd.aspek = pba.aspek";

                                                $result = DB::select($sql, [
                                                    'responden_id_sql_param' => $respondenItem->id,
                                                    'remark_sql_param' => $remark,
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
                                                    {{-- PENAMBAHAN: Sel untuk menampilkan catatan_aspek --}}
                                                    <td style="text-align: left; white-space: pre-wrap;">
                                                        {{ $data->catatan_aspek ?? '-' }}</td>
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
                                                    {{-- PENAMBAHAN: Sel untuk menampilkan catatan_aspek --}}
                                                    <td style="text-align: left; white-space: pre-wrap;">
                                                        {{ $data->catatan_aspek ?? '-' }}</td>
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
    <script>
        // Inisialisasi DataTables jika diperlukan (opsional untuk tampilan ini)
        // $(document).ready(function() {
        //     $('#data-table').DataTable();
        // });
    </script>
@endpush
