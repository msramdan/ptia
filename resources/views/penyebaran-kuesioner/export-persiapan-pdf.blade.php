<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Persiapan Evaluasi - {{ $project->kaldikDesc }}</title>
    <style>
        /* --- Base Page Setup --- */
        @page {
            margin: 25mm 20mm 25mm 20mm;
            /* Margin: Atas, Kanan, Bawah, Kiri */
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            /* Default Font Isi */
            font-size: 12px;
            /* Default Font Size Isi */
            line-height: 1.4;
            /* Jarak antar baris default */
            margin: 0;
            color: #000;
            /* Warna teks default */
        }

        table {
            /* Basic table reset */
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        /* Ulangi header tabel */
        tfoot {
            display: table-footer-group;
        }

        th,
        td {
            padding: 3px 5px;
            /* Padding lebih rapat */
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* --- Header Styling (Original/Arial) --- */
        .table-header {
            border: none;
            margin-bottom: 5px;
            font-size: 13px;
            /* Ukuran default header */
        }

        .table-header td {
            border: none;
            vertical-align: middle;
            /* Tengahkan vertikal */
            padding: 0 5px;
        }

        .logo-cell {
            width: 80px;
            text-align: right;
        }

        .logo {
            width: auto;
            height: auto;
        }

        .header-text-cell {
            text-align: center;
            font-size: 14px;
        }

        /* Ukuran teks alamat */
        .header-instansi {
            font-size: 15px;
            font-weight: bold;
            line-height: 1.2;
        }

        /* Nama instansi */
        .header-address {
            font-size: 13px;
            color: #333;
            line-height: 1.3;
            margin-top: 2px;
        }

        .header-contact {
            font-size: 13px;
        }

        /* Ukuran email/web */
        .header-contact span {
            color: #003ea2;
        }

        hr.header-line {
            border: none;
            border-top: 2px solid #000;
            margin: 5px 0 15px 0;
        }

        /* --- Styling Isi Konten (Times New Roman 12px, Rapat) --- */
        .creator-info {
            font-size: 10px;
            /* Info pembuat kecil saja */
            color: #555;
            margin-bottom: 15px;
            font-family: 'Times New Roman', Times, serif;
        }

        .doc-title {
            text-align: center;
            font-size: 14px;
            /* Judul dokumen sedikit lebih besar */
            margin-bottom: 20px;
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.3;
        }

        .doc-title b {
            font-weight: bold;
        }

        .section-title {
            font-size: 13px;
            /* Judul Bagian (A, B) */
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            font-family: 'Times New Roman', Times, serif;
        }

        .sub-section-title {
            font-weight: bold;
            padding-left: 15px;
            /* Indentasi sub judul */
            font-size: 12px;
            font-family: 'Times New Roman', Times, serif;
            /* Mengurangi jarak vertikal di sekitar sub-judul */
            margin-bottom: 3px !important;
            margin-top: 8px !important;
        }

        .content-table-container {
            /* Wadah untuk tabel & teks WA */
            margin-left: 30px;
            /* Indentasi konten */
            width: calc(100% - 30px);
        }

        /* Styling untuk tabel berborder di konten */
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #333;
            font-size: 11px;
            /* Font tabel lebih kecil agar rapat */
            font-family: 'Times New Roman', Times, serif;
        }

        .table-bordered thead th {
            background-color: #EAEAEA;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        /* Penyesuaian lebar kolom tabel kuesioner */
        .kuesioner-table td:nth-child(1),
        .kuesioner-table th:nth-child(1) {
            width: 30px;
            text-align: center;
        }

        /* No */
        .kuesioner-table td:nth-child(2),
        .kuesioner-table th:nth-child(2) {
            width: 90px;
        }

        /* Kode */
        .kuesioner-table td:nth-child(3),
        .kuesioner-table th:nth-child(3) {
            width: 110px;
        }

        /* Aspek */
        .kuesioner-table td:nth-child(4),
        .kuesioner-table th:nth-child(4) {
            width: 90px;
        }

        /* Kriteria */
        /* Pertanyaan akan mengisi sisa */

        .wa-template {
            border: 1px solid #ccc;
            padding: 6px 8px;
            background-color: #f9f9f9;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
            /* Font TNR untuk WA */
            font-size: 11px;
            /* Font template WA */
            line-height: 1.4;
        }

        .wa-template p {
            margin: 0 0 3px 0 !important;
            padding: 0 !important;
        }

        .wa-template p:last-child {
            margin-bottom: 0 !important;
        }

        /* Wadah baru khusus untuk WA Template */
        .wa-template-container {
            margin-left: 30px;
            /* Samakan indentasi dengan .content-table-container */
            width: calc(100% - 30px);
            margin-top: 2px;
            /* Kurangi jarak dari sub-judul di atasnya */
            margin-bottom: 8px;
            /* Kurangi jarak ke elemen di bawahnya */
        }

        .progress-table-container {
            /* Wadah khusus progress agar lebarnya bisa diatur */
            margin-left: 30px;
            width: 300px;
            /* Lebar tabel progress */
        }

        .progress-table td {
            width: 60%;
            /* Kolom label lebih lebar */
            font-size: 11px;
            font-family: 'Times New Roman', Times, serif;
        }

        .progress-table td:last-child {
            width: 40%;
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <table class="table-header"> {{-- Tambahkan class ini --}}
        <tr>
            <td class="logo-cell"> {{-- Tambahkan class ini --}}
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo BPKP" class="logo">
                @endif
            </td>
            <td class="header-text-cell"> {{-- Tambahkan class ini --}}
                {{-- Beri class pada nama instansi --}}
                <div class="header-instansi">BADAN PENGAWASAN KEUANGAN DAN PEMBANGUNAN</div>
                <div class="header-instansi">PUSAT PENDIDIKAN DAN PELATIHAN PENGAWASAN</div>
                <div class="header-address">
                    Jalan Beringin II, Pandansari, Ciawi, Kab. Bogor<br>
                    Telepon (0251) 8249001–3 Fax. (0251) 8248986<br>
                    {{-- Beri class pada kontak --}}
                    <span class="header-contact">email: <span>pusdiklatwas@bpkp.go.id</span> web:
                        <span>http://pusdiklatwas.bpkp.go.id</span></span>
                </div>
            </td>
        </tr>
        <tr>
            {{-- Beri class pada garis --}}
            <td colspan="2" style="border: none; padding: 0;">
                <hr class="header-line">
            </td>
        </tr>
    </table>

    <div class="creator-info">Dokumen dibuat oleh: {{ $namaPembuat }} pada {{ $tanggalCetak }}</div>

    <div class="doc-title"> {{-- Tambahkan class ini --}}
        <b>PERSIAPAN EVALUASI PASCA PEMBELAJARAN</b><br>
        <b>{{ $project->kaldikDesc ?? 'Nama Pelatihan Belum Ada' }}</b><br>
        Kode Project: {{ $project->kaldikID ?? '-' }}
        {{ $project->created_at ? $project->created_at->format('Y-m-d') : '' }}
    </div>

    <div class="section-title">A. Alumni</div>

    <div class="sub-section-title">1. Kuesioner</div>
    <div class="content-table-container"> {{-- Wadah konten --}}
        {{-- Tambahkan class pada tabel kuesioner --}}
        <table class="table-bordered kuesioner-table">
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
                        <td>{{ $project->kaldikID ?? 'N/A' }}</td>
                        <td>{{ $kuesioner->aspek_nama ?? '-' }}</td>
                        <td>{{ $kuesioner->kriteria ?? '-' }}</td>
                        <td>{!! $kuesioner->pertanyaan ?? '-' !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data kuesioner Alumni.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="sub-section-title">2. Redaksi Whatsapp Blasting</div>
    {{-- Tambahkan div container BARU di sini --}}
    <div class="wa-template-container">
        <div class="wa-template">
            {!! $templateAlumni !!} {{-- Atau {{ }} --}}
        </div>
    </div>

    <div class="sub-section-title">3. Progress Keterisian Kuesioner</div>
    <div class="progress-table-container"> {{-- Wadah tabel progress --}}
        <table class="table-bordered progress-table"> {{-- Tambahkan class --}}
            <tr>
                <td>Alumni Teridentifikasi</td>
                <td>{{ number_format($totalAlumni, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Responden Mengisi</td>
                <td>{{ number_format($mengisiAlumni, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>% Keterisian</td>
                <td>{{ number_format($persenAlumni, 2, ',', '.') }}%</td>
            </tr>
        </table>
    </div>

    <div class="section-title">B. Atasan Langsung</div>

    <div class="sub-section-title">1. Kuesioner</div>
    <div class="content-table-container"> {{-- Wadah konten --}}
        {{-- Tambahkan class pada tabel kuesioner --}}
        <table class="table-bordered kuesioner-table">
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
                        <td>{{ $project->kaldikID ?? 'N/A' }}</td>
                        <td>{{ $kuesioner->aspek_nama ?? '-' }}</td>
                        <td>{{ $kuesioner->kriteria ?? '-' }}</td>
                        <td>{!! $kuesioner->pertanyaan ?? '-' !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data kuesioner Atasan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="sub-section-title">2. Redaksi Whatsapp Blasting</div>
    {{-- Tambahkan div container BARU di sini --}}
    <div class="wa-template-container">
        <div class="wa-template">
            {!! $templateAtasan !!} {{-- Atau {{ }} --}}
        </div>
    </div>

    <div class="sub-section-title">3. Progress Keterisian Kuesioner</div>
    <div class="progress-table-container"> {{-- Wadah tabel progress --}}
        <table class="table-bordered progress-table"> {{-- Tambahkan class --}}
            <tr>
                <td>Atasan Teridentifikasi</td>
                <td>{{ number_format($totalAtasan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Atasan Mengisi</td>
                <td>{{ number_format($mengisiAtasan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>% Keterisian</td>
                <td>{{ number_format($persenAtasan, 2, ',', '.') }}%</td>
            </tr>
        </table>
    </div>

</body>

</html>
