<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Penyebaran Kuesioner - {{ $project->kaldikDesc ?? 'N/A' }}</title>
    <style>
        /* --- CSS Sama Seperti Sebelumnya --- */
        @page {
            margin: 25mm 20mm 25mm 20mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            line-height: 1.3;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 8px;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th,
        td {
            padding: 2px 4px;
            border: 1px solid #333;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #EAEAEA;
            text-align: center;
            font-weight: bold;
        }

        .table-noborder,
        .table-noborder td,
        .table-noborder th {
            border: none;
        }

        .table-header td {
            vertical-align: middle;
            padding: 0 5px;
        }

        .logo {
            width: 150px;
            height: auto;
        }

        .header-text-cell {
            text-align: center;
        }

        .header-instansi {
            font-size: 13px;
            font-weight: bold;
            line-height: 1.1;
        }

        .header-address {
            font-size: 11px;
            line-height: 1.2;
            margin-top: 1px;
        }

        .header-contact {
            font-size: 11px;
        }

        .header-contact span {
            color: #003ea2;
        }

        hr.header-line {
            border: none;
            border-top: 2px solid #000;
            margin: 5px 0 10px 0;
        }

        .doc-title {
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .sub-section-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 3px;
            margin-left: 15px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Atur lebar kolom spesifik */
        /* Daftar Responden */
        .daftar-responden td:nth-child(1) {
            width: 5%;
            text-align: center;
        }

        /* No */
        .daftar-responden td:nth-child(2) {
            width: 15%;
        }

        /* NIP */
        .daftar-responden td:nth-child(3) {
            width: 25%;
        }

        /* Nama */
        .daftar-responden td:nth-child(4) {
            width: 15%;
        }

        /* Jabatan */
        .daftar-responden th:nth-child(5),
        /* Kolom Pangkat Header */
        .daftar-responden td:nth-child(5) {
            width: 10%;
        }

        /* Pangkat Data */
        .daftar-responden td:nth-child(6) {
            width: 15%;
        }

        /* Telepon */
        .daftar-responden td:nth-child(7) {
            width: 20%;
        }

        /* Unit */

        /* Kuesioner */
        .kuesioner-table td:nth-child(1) {
            width: 5%;
            text-align: center;
        }

        /* No */
        .kuesioner-table td:nth-child(2) {
            width: 15%;
        }

        /* Kode */
        .kuesioner-table td:nth-child(3) {
            width: 15%;
        }

        /* Aspek */
        .kuesioner-table td:nth-child(4) {
            width: 15%;
        }

        /* Kriteria */
        .kuesioner-table td:nth-child(5) {
            width: 50%;
        }

        /* Pertanyaan */

        /* Bobot Table Styling */
        .bobot-table {
            border: 1px solid #333;
        }

        /* Border luar tabel bobot */
        .bobot-table th,
        .bobot-table td {
            border: 1px solid #333;
        }

        /* Border dalam sel */
        .bobot-level-header {
            font-weight: bold;
            background-color: #EAEAEA;
        }

        .bobot-group-header {
            font-weight: bold;
        }

        .bobot-item {
            padding-left: 15px;
        }
    </style>
</head>

<body>
    {{-- Header Dokumen --}}
    <table class="table-noborder table-header">
        <tr>
            <td style="width: 20%; text-align: right; padding-right: 10px;">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="logo">
                @endif
            </td>
            <td class="header-text-cell" style="width: 60%;">
                <div class="header-instansi">BADAN PENGAWASAN KEUANGAN DAN PEMBANGUNAN</div>
                <div class="header-instansi">PUSAT PENDIDIKAN DAN PELATIHAN PENGAWASAN</div>
                <div class="header-address">
                    Jalan Beringin II, Pandansari, Ciawi, Kab. Bogor<br>
                    Telepon (0251) 8249001–3 Fax. (0251) 8248986<br>
                    <span class="header-contact">email: <span>pusdiklatwas@bpkp.go.id</span> web:
                        <span>http://pusdiklatwas.bpkp.go.id</span></span>
                </div>
            </td>
            <td style="width: 20%;"></td>
        </tr>
        <tr>
            <td colspan="3" style="padding: 0;">
                <hr class="header-line">
            </td>
        </tr>
    </table>

    {{-- Judul Dokumen --}}
    <div class="doc-title">
        PENYEBARAN KUESIONER<br>
        Pelatihan {{ $project->kaldikDesc ?? 'N/A' }}<br>
        Kode Project: {{ $project->kaldikID ?? '-' }} {{ $projectCreatedAt }}
    </div>

    {{-- A. Kriteria Responden --}}
    <div class="section-title">A. Kriteria Responden</div>
    <div class="sub-section-title">a. Filter Pre-test dan Post-test</div>

    {{-- Tabel Kriteria Baru --}}
    <table style="margin-left: 15px; width: auto;">
        <thead>
            <tr>
                <th style="width: 60%;">Kriteria</th>
                <th style="width: 40%;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Nilai minimal pre-test</td>
                <td>{{ $kriteriaResponden->nilai_pre_test_minimal ?? '(tidak diatur)' }}</td>
            </tr>
            <tr>
                <td>Nilai minimal post-test</td>
                <td>{{ $kriteriaResponden->nilai_post_test_minimal ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nilai kenaikan pre-test dan post test</td>
                <td>{{ !empty($kriteriaNilaiPostTest) ? implode(', ', $kriteriaNilaiPostTest) : '-' }}</td>
            </tr>
        </tbody>
    </table>
    {{-- B. Daftar Responden Alumni --}}
    <div class="section-title">B. Daftar Responden Alumni</div>
    <table class="daftar-responden">
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Pangkat</th>
                <th>Telepon</th>
                <th>Unit Kerja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarResponden as $responden)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $responden->nip ?? '-' }}</td>
                    <td>{{ $responden->nama ?? '-' }}</td>
                    <td>{{ $responden->jabatan ?? '-' }}</td>
                    {{-- Tampilkan data pangkat jika ada di $responden (cek query controller)
                      Jika tidak ada, tampilkan '-' --}}
                    <td>{{ $responden->pangkat ?? '-' }}</td>
                    <td>{{ $responden->telepon ?? '-' }}</td>
                    <td>{{ $responden->unit ?? '-' }}</td>
                </tr>
            @empty
                {{-- Kolom span disesuaikan karena ada 7 kolom sekarang --}}
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data responden.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- C. Kuesioner --}}
    <div class="section-title">C. Kuesioner</div>
    <div class="sub-section-title">a. Alumni</div>
    <table class="kuesioner-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Kuesioner</th>
                <th>Aspek</th>
                <th>Kriteria</th>
                <th>Pertanyaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kuesionerAlumni as $kuesioner)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kuesioner->kode_kuesioner ?? '-' }}</td>
                    <td>{{ $kuesioner->aspek_nama ?? '-' }}</td>
                    <td>{{ $kuesioner->kriteria ?? '-' }}</td>
                    <td>{!! $kuesioner->pertanyaan ?? '-' !!}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data kuesioner alumni.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-before: auto;"></div>

    <div class="sub-section-title">b. Atasan alumni</div>
    <table class="kuesioner-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Kuesioner</th>
                <th>Aspek</th>
                <th>Kriteria</th>
                <th>Pertanyaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kuesionerAtasan as $kuesioner)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kuesioner->kode_kuesioner ?? '-' }}</td>
                    <td>{{ $kuesioner->aspek_nama ?? '-' }}</td>
                    <td>{{ $kuesioner->kriteria ?? '-' }}</td>
                    <td>{!! $kuesioner->pertanyaan ?? '-' !!}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data kuesioner atasan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-before: auto;"></div>

    {{-- D. Bobot --}}
    <div class="section-title">D. Bobot</div>
    <table class="bobot-table">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th style="width: 20%;">Bobot</th>
            </tr>
        </thead>
        <tbody>
            {{-- LEVEL 3 --}}
            <tr class="bobot-level-header">
                <td>LEVEL 3 (Data Primer)</td>
                <td class="text-right">100%</td> {{-- Asumsi Total selalu 100% --}}
            </tr>
            <tr>
                <td class="bobot-group-header">1. Alumni</td>
                <td class="text-right">{{ number_format($bobotLevel3->sum('bobot_alumni'), 2) }}%</td>
            </tr>
            @forelse($bobotLevel3 as $bobot)
                <tr>
                    <td class="bobot-item">{{ chr(96 + $loop->iteration) }}. {{ $bobot->aspek ?? '-' }}</td>
                    <td class="text-right">{{ number_format($bobot->bobot_alumni, 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">(Data bobot alumni level 3 tidak tersedia)</td>
                </tr>
            @endforelse
            <tr>
                <td class="bobot-group-header">2. Atasan Langsung</td>
                <td class="text-right">{{ number_format($bobotLevel3->sum('bobot_atasan_langsung'), 2) }}%</td>
            </tr>
            @forelse($bobotLevel3 as $bobot)
                <tr>
                    <td class="bobot-item">{{ chr(96 + $loop->iteration) }}. {{ $bobot->aspek ?? '-' }}</td>
                    <td class="text-right">{{ number_format($bobot->bobot_atasan_langsung, 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">(Data bobot atasan level 3 tidak tersedia)</td>
                </tr>
            @endforelse

            {{-- LEVEL 4 --}}
            <tr class="bobot-level-header">
                <td>LEVEL 4 (Data Primer & Sekunder)</td>
                <td class="text-right">100%</td> {{-- Asumsi Total selalu 100% --}}
            </tr>
            <tr>
                <td class="bobot-group-header">Data Primer: Kuesioner</td>
                <td class="text-right">
                    {{ number_format($bobotLevel4->sum('bobot_alumni') + $bobotLevel4->sum('bobot_atasan_langsung'), 2) }}%
                </td>
            </tr>
            <tr>
                <td class="bobot-item" style="font-weight: bold;">1. Alumni</td>
                <td class="text-right">{{ number_format($bobotLevel4->sum('bobot_alumni'), 2) }}%</td>
            </tr>
            @forelse($bobotLevel4 as $bobot)
                <tr>
                    <td style="padding-left: 30px;">{{ chr(100 + $loop->iteration) }}. {{ $bobot->aspek ?? '-' }}</td>
                    <td class="text-right">{{ number_format($bobot->bobot_alumni, 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="padding-left: 15px;">(Data bobot alumni level 4 tidak tersedia)</td>
                </tr>
            @endforelse
            <tr>
                <td class="bobot-item" style="font-weight: bold;">2. Atasan Langsung</td>
                <td class="text-right">{{ number_format($bobotLevel4->sum('bobot_atasan_langsung'), 2) }}%</td>
            </tr>
            @forelse($bobotLevel4 as $bobot)
                <tr>
                    <td style="padding-left: 30px;">{{ chr(100 + $loop->iteration) }}. {{ $bobot->aspek ?? '-' }}</td>
                    <td class="text-right">{{ number_format($bobot->bobot_atasan_langsung, 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="padding-left: 15px;">(Data bobot atasan level 4 tidak tersedia)</td>
                </tr>
            @endforelse
            <tr>
                <td class="bobot-group-header">Data Sekunder: Capaian Kinerja</td>
                <td class="text-right">{{ number_format($bobotSekunder->bobot_aspek_sekunder ?? 0, 2) }}%</td>
            </tr>
            {{-- Asumsi hanya satu aspek sekunder --}}
            <tr>
                <td class="bobot-item">a. Hasil Pelatihan</td>
                <td class="text-right">{{ number_format($bobotSekunder->bobot_aspek_sekunder ?? 0, 2) }}%</td>
            </tr>

        </tbody>
    </table>

</body>

</html>
