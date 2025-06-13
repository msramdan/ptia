<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 25mm 20mm 20mm 20mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
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
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
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

        .creator-info {
            font-size: 10px;
            color: #555;
            margin-bottom: 15px;
        }

        .doc-title {
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #333;
        }

        .table-bordered thead th {
            background-color: #EAEAEA;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bobot-table th,
        .bobot-table td {
            border: 1px solid #333;
        }

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

        .page-break {
            page-break-after: always;
        }

        .info-table td {
            border: none;
            padding: 3px 0;
        }
    </style>
</head>

<body>
    {{-- KOP SURAT --}}
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

    <div class="creator-info">Dokumen Evaluator: {{ $namaPembuat }} | Dicetak pada: {{ $tanggalCetak }}</div>

    <div class="doc-title">
        LAPORAN LENGKAP HASIL EVALUASI PASCA PEMBELAJARAN<br>
        {{ $hasilEvaluasi->kaldikDesc ?? 'N/A' }}
    </div>

    <div class="section-title">A. Informasi Umum</div>
    <table class="info-table">
        <tr>
            <td width="25%"><strong>Kode Proyek</strong></td>
            <td width="1%">:</td>
            <td>{{ $hasilEvaluasi->kode_project }}</td>
        </tr>
        <tr>
            <td><strong>Evaluator</strong></td>
            <td>:</td>
            <td>{{ $hasilEvaluasi->user_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Jenis Diklat</strong></td>
            <td>:</td>
            <td>{{ $hasilEvaluasi->nama_diklat_type ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Selesai Diklat</strong></td>
            <td>:</td>
            <td>{{ $hasilEvaluasi->tanggal_selesai ? \Carbon\Carbon::parse($hasilEvaluasi->tanggal_selesai)->isoFormat('D MMMM YYYY') : '-' }}
            </td>
        </tr>
    </table>

    <div class="section-title">B. Rangkuman Skor Evaluasi</div>
    <table class="table-bordered">
        <thead>
            <tr>
                <th>Level Evaluasi</th>
                <th>Skor Rata-Rata</th>
                <th>Kriteria Dampak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Level 3</td>
                <td class="text-center">{{ number_format($hasilEvaluasi->avg_skor_level_3, 2) }}</td>
                <td class="text-center">{{ $hasilEvaluasi->kriteria_dampak_level_3 }}</td>
            </tr>
            <tr>
                <td>Level 4</td>
                <td class="text-center">{{ number_format($hasilEvaluasi->avg_skor_level_4, 2) }}</td>
                <td class="text-center">{{ $hasilEvaluasi->kriteria_dampak_level_4 }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">C. Data Sekunder</div>
    <table class="table-bordered">
        <tr>
            <td width="30%"><strong>Nilai Kinerja Awal</strong></td>
            <td>{{ $dataSekunder->nilai_kinerja_awal ?? '-' }} (Periode: {{ $dataSekunder->periode_awal ?? '-' }})
            </td>
        </tr>
        <tr>
            <td><strong>Nilai Kinerja Akhir</strong></td>
            <td>{{ $dataSekunder->nilai_kinerja_akhir ?? '-' }} (Periode: {{ $dataSekunder->periode_akhir ?? '-' }})
            </td>
        </tr>
        <tr>
            <td><strong>Status Kinerja</strong></td>
            <td><strong>{{ $statusDataSekunder }}</strong></td>
        </tr>
        <tr>
            <td><strong>Satuan</strong></td>
            <td>{{ $dataSekunder->satuan ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Sumber Data</strong></td>
            <td>{{ $dataSekunder->sumber_data ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>PIC Unit Kerja</strong></td>
            <td>{{ $dataSekunder->nama_pic ?? '-' }} (Telp: {{ $dataSekunder->telpon ?? '-' }})</td>
        </tr>
    </table>

    <div class="section-title">D. Bobot Penilaian</div>
    <table class="table-bordered bobot-table">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th style="width: 20%;">Bobot</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bobot-level-header">
                <td colspan="2">LEVEL 3 (DATA PRIMER)</td>
            </tr>
            <tr>
                <td class="bobot-group-header" style="padding-left: 15px;">1. Alumni</td>
                <td class="text-right">{{ number_format($bobotLevel3->sum('bobot_alumni'), 2) }}%</td>
            </tr>
            @forelse($bobotLevel3 as $bobot)
                <tr>
                    <td class="bobot-item" style="padding-left: 30px;">{{ $bobot->aspek ?? '-' }}</td>
                    <td class="text-right">{{ number_format($bobot->bobot_alumni, 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">(Data tidak tersedia)</td>
                </tr>
            @endforelse
            <tr>
                <td class="bobot-group-header" style="padding-left: 15px;">2. Atasan Langsung</td>
                <td class="text-right">{{ number_format($bobotLevel3->sum('bobot_atasan_langsung'), 2) }}%</td>
            </tr>
            @forelse($bobotLevel3 as $bobot)
                <tr>
                    <td class="bobot-item" style="padding-left: 30px;">{{ $bobot->aspek ?? '-' }}</td>
                    <td class="text-right">{{ number_format($bobot->bobot_atasan_langsung, 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">(Data tidak tersedia)</td>
                </tr>
            @endforelse
            <tr class="bobot-level-header">
                <td colspan="2">LEVEL 4 (DATA PRIMER & SEKUNDER)</td>
            </tr>
            <tr>
                <td class="bobot-group-header">Data Primer: Kuesioner</td>
                <td class="text-right">
                    {{ number_format($bobotLevel4->sum('bobot_alumni') + $bobotLevel4->sum('bobot_atasan_langsung'), 2) }}%
                </td>
            </tr>
            <tr>
                <td class="bobot-group-header" style="padding-left: 15px;">1. Alumni</td>
                <td class="text-right">{{ number_format($bobotLevel4->sum('bobot_alumni'), 2) }}%</td>
            </tr>
            @forelse($bobotLevel4 as $bobot)
                <tr>
                    <td class="bobot-item" style="padding-left: 30px;">{{ $bobot->aspek ?? '-' }}</td>
                    <td class="text-right">{{ number_format($bobot->bobot_alumni, 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center" style="padding-left: 15px;">(Data tidak tersedia)</td>
                </tr>
            @endforelse
            <tr>
                <td class="bobot-group-header" style="padding-left: 15px;">2. Atasan Langsung</td>
                <td class="text-right">{{ number_format($bobotLevel4->sum('bobot_atasan_langsung'), 2) }}%</td>
            </tr>
            @forelse($bobotLevel4 as $bobot)
                <tr>
                    <td class="bobot-item" style="padding-left: 30px;">{{ $bobot->aspek ?? '-' }}</td>
                    <td class="text-right">{{ number_format($bobot->bobot_atasan_langsung, 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center" style="padding-left: 15px;">(Data tidak tersedia)</td>
                </tr>
            @endforelse
            <tr class="bobot-level-header">
                <td colspan="2">Data Sekunder: Capaian Kinerja</td>
            </tr>
            <tr>
                <td class="bobot-item">a. Hasil Pelatihan</td>
                <td class="text-right">{{ number_format($bobotSekunder->bobot_aspek_sekunder ?? 0, 2) }}%</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title">E. DAFTAR RESPONDEN</div>
    <strong>Alumni (Total: {{ count($alumni) }} Orang)</strong>
    <table class="table-bordered">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Jabatan & Unit Kerja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumni as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->nip }}</td>
                    <td>{{ $item->jabatan }} - {{ $item->unit }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada responden alumni.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <strong>Atasan (Total: {{ count($atasan) }} Orang)</strong>
    <table class="table-bordered">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Atasan</th>
                <th>Telepon</th>
            </tr>
        </thead>
        <tbody>
            @forelse($atasan as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_atasan }}</td>
                    <td>{{ $item->telepon_atasan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada responden atasan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
