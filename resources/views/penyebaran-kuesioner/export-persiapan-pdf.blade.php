<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Persiapan Evaluasi - {{ $project->kaldikDesc }}</title>
    <style>
        /* --- CSS dari file .doc --- */
        body {
            font-family: Arial, sans-serif;
            margin: 0px;
            font-size: 12px;
        }

        html {
            margin-bottom: 0px;
        }

        .font12 {
            font-size: 12px;
            font-family: Arial;
        }

        .font13 {
            font-size: 13px;
            font-family: Arial;
        }

        .font14 {
            font-size: 14px;
            font-family: Arial;
        }

        .font15 {
            font-size: 15px;
            font-family: Arial;
        }

        /* --- Akhir CSS dari .doc --- */

        /* --- CSS Tambahan untuk PDF --- */
        @page {
            margin: 30px 50px;
            /* Atur margin: atas, kanan/kiri, bawah */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            padding: 5px 8px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            /* Wrap text */
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #333;
        }

        /* Border lebih gelap */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .w-50 {
            width: 50px;
        }

        .w-150 {
            width: 150px;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .sub-section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            padding-left: 10px;
        }

        .content-table {
            margin-left: 25px;
            width: calc(100% - 25px);
        }

        .wa-template {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
            white-space: pre-wrap;
            /* Penting untuk menjaga format teks template */
            word-wrap: break-word;
            font-family: 'Courier New', Courier, monospace;
            /* Font untuk template */
            margin-left: 25px;
            width: calc(100% - 50px);
            /* Disesuaikan dengan padding */
            line-height: 1.4;
        }

        .progress-table td {
            width: 50%;
        }

        hr {
            border: none;
            border-top: 2px solid #000;
            margin: 10px 0;
        }

        .logo {
            width: 50px;
            height: auto;
        }

        .header-text {
            text-align: center;
            vertical-align: top;
        }

        .header-address {
            font-size: 13px;
            color: #555;
            line-height: 1.3;
        }

        .header-contact span {
            color: #003ea2;
        }

        .page-break {
            page-break-after: always;
        }

        thead tr {
            background-color: #EAEAEA;
        }

        /* Warna header tabel */
    </style>
</head>

<body>
    <table style="border: none;">
        <tr>
            <td style="width: 80px; text-align: right; vertical-align: top; border: none;">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo BPKP" class="logo">
                @endif
            </td>
            <td class="header-text" style="border: none;">
                <div class="font15" style="font-weight: bold; margin-bottom: 2px;">BADAN PENGAWASAN KEUANGAN DAN
                    PEMBANGUNAN</div>
                <div class="font15" style="font-weight: bold; margin-bottom: 5px;">PUSAT PENDIDIKAN DAN PELATIHAN
                    PENGAWASAN</div>
                <div class="header-address">
                    Jalan Beringin II, Pandansari, Ciawi, Kab. Bogor<br>
                    Telepon (0251) 8249001–3 Fax. (0251) 8248986<br>
                    <span class="font13">email: <span class="header-contact">pusdiklatwas@bpkp.go.id</span> web: <span
                            class="header-contact">http://pusdiklatwas.bpkp.go.id</span></span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border: none; padding: 0;">
                <hr style="margin-top: 5px;">
            </td>
        </tr>
    </table>
    <div style="font-size: 11px; color: #666; margin-bottom: 15px;">Dokumen dibuat oleh: {{ $namaPembuat }} pada
        {{ $tanggalCetak }}</div>

    <div class="text-center font14" style="margin-bottom: 25px;">
        <b class="font15">PERSIAPAN EVALUASI PASCA PEMBELAJARAN</b><br>
        <b class="font15">{{ $project->kaldikDesc ?? 'Nama Pelatihan Belum Ada' }}</b><br>
        Kode Project: {{ $project->kaldikID ?? '-' }}
        {{ $project->created_at ? $project->created_at->format('Y-m-d') : '' }}
    </div>

    <div class="section-title">A. Alumni</div>

    <div class="sub-section-title">1. Kuesioner</div>
    <div class="content-table">
        <table class="table-bordered font12">
            <thead>
                <tr>
                    <th class="w-50 text-center">No</th>
                    <th class="w-150">Kode Kuesioner</th>
                    <th class="w-150">Aspek</th>
                    <th>Kriteria</th>
                    <th>Pertanyaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kuesionerAlumni as $kuesioner)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $project->kaldikID ?? 'N/A' }}</td> {{-- Kode Diklat --}}
                        <td>{{ $kuesioner->aspek_nama ?? ($kuesioner->aspek ?? '-') }}</td> {{-- Sesuaikan jika ada relasi aspek --}}
                        <td>{{ $kuesioner->kriteria ?? '-' }}</td>
                        <td>{!! $kuesioner->pertanyaan ?? '-' !!}</td> {{-- Hati-hati jika pertanyaan mengandung HTML --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 10px;">Tidak ada data kuesioner untuk
                            Alumni.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="sub-section-title">2. Redaksi Whatsapp Blasting</div>
    <div class="wa-template font12">
        {!! $templateAlumni !!}
    </div>

    <div class="sub-section-title">3. Progress Keterisian Kuesioner</div>
    <div style="margin-left: 25px; width: 350px;">
        <table class="table-bordered font12 progress-table">
            <tr>
                <td>Alumni Teridentifikasi</td>
                <td class="text-right">{{ number_format($totalAlumni, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Responden Mengisi</td>
                <td class="text-right">{{ number_format($mengisiAlumni, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>% Keterisian</td>
                <td class="text-right">{{ number_format($persenAlumni, 2, ',', '.') }}%</td>
            </tr>
        </table>
    </div>

    {{-- Uncomment jika perlu pemisah halaman --}}
    {{-- <div class="page-break"></div> --}}

    <div class="section-title" style="margin-top: 30px;">B. Atasan Langsung</div>

    <div class="sub-section-title">1. Kuesioner</div>
    <div class="content-table">
        <table class="table-bordered font12">
            <thead>
                <tr>
                    <th class="w-50 text-center">No</th>
                    <th class="w-150">Kode Kuesioner</th>
                    <th class="w-150">Aspek</th>
                    <th>Kriteria</th>
                    <th>Pertanyaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kuesionerAtasan as $kuesioner)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $project->kaldikID ?? 'N/A' }}</td> {{-- Kode Diklat --}}
                        <td>{{ $kuesioner->aspek_nama ?? ($kuesioner->aspek ?? '-') }}</td>
                        <td>{{ $kuesioner->kriteria ?? '-' }}</td>
                        <td>{!! $kuesioner->pertanyaan ?? '-' !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 10px;">Tidak ada data kuesioner untuk
                            Atasan Langsung.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="sub-section-title">2. Redaksi Whatsapp Blasting</div>
    <div class="wa-template font12">
        {!! $templateAtasan !!}
    </div>

    <div class="sub-section-title">3. Progress Keterisian Kuesioner</div>
    <div style="margin-left: 25px; width: 350px;">
        <table class="table-bordered font12 progress-table">
            <tr>
                <td>Atasan Teridentifikasi</td>
                <td class="text-right">{{ number_format($totalAtasan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Atasan Mengisi</td>
                <td class="text-right">{{ number_format($mengisiAtasan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>% Keterisian</td>
                <td class="text-right">{{ number_format($persenAtasan, 2, ',', '.') }}%</td>
            </tr>
        </table>
    </div>

</body>

</html>
