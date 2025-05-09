<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Evaluasi Responden</title> {{-- Judul lebih deskriptif --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Memuat Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Add gradient background with smooth animation */
        body {
            background: linear-gradient(45deg, #A2C2E5, #B4D8F3);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            min-height: 100vh;
            /* Pastikan background mengisi seluruh tinggi */
            padding-bottom: 30px;
            /* Beri jarak bawah */
        }

        /* Animation for background gradient */
        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .check-circle {
            width: 80px;
            /* Sedikit lebih kecil */
            height: 80px;
            margin: auto;
            margin-bottom: 15px;
            animation: zoomIn 0.6s ease;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.5);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .thank-you-text h1 {
            font-size: 1.8rem;
            /* Sedikit lebih kecil */
        }

        .thank-you-text p {
            font-size: 0.95rem;
            /* Sedikit lebih kecil */
        }

        .table-sm td,
        .table-sm th {
            padding: 0.4rem;
            /* Sedikit lebih luas */
            font-size: 0.8rem;
            /* Sedikit lebih kecil */
            vertical-align: middle;
        }

        .table-sm th {
            font-weight: 600;
            /* Lebih tebal header tabel */
        }


        .accordion-button {
            font-size: 0.95rem;
            /* Sedikit lebih kecil */
            font-weight: 600;
        }

        .accordion-button:not(.collapsed) {
            color: #052c65;
            background-color: #cfe2ff;
            /* Warna saat terbuka */
        }

        .container-sm {
            max-width: 850px;
            /* Sedikit lebih lebar */
            background-color: rgba(255, 255, 255, 0.95);
            /* Lebih solid */
            padding: 25px;
            /* Padding lebih besar */
            border-radius: 12px;
            /* Radius lebih besar */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            /* Shadow lebih jelas */
            margin-top: 20px;
            /* Jarak dari atas */
        }

        .info-card .list-group-item {
            background-color: transparent !important;
            /* Transparan untuk list group info */
            border: none !important;
            /* Hilangkan border */
            padding-left: 0;
            padding-right: 0;
        }

        .info-card .badge {
            min-width: 130px;
            /* Lebar minimum badge agar rata */
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container-sm text-center thank-you-text my-4"> {{-- my-4 untuk margin atas bawah --}}
        <svg class="check-circle text-success" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
            viewBox="0 0 16 16"> {{-- Ganti ke fill --}}
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </svg>
        <h1 class="fw-bold text-success">Terima Kasih!</h1>
        <p class="text-muted">Kuesioner Anda telah berhasil dikirim. Berikut adalah hasil evaluasi Anda.</p>
    </div>

    <div class="container-sm">

        {{-- Informasi Kriteria Penilaian Predikat --}}
        <div class="card mb-4 shadow-sm alert alert-light info-card" style="border-left: 5px solid #6c757d;">
            <div class="card-body p-3">
                <h6 class="card-title fw-bold mb-2"><i class="fas fa-info-circle me-1"></i>Informasi Kriteria Penilaian
                    Predikat</h6>
                <p class="card-text mb-2" style="font-size: 0.9rem;">
                    Predikat dampak ditentukan berdasarkan rentang skor total yang diperoleh, sesuai dengan aturan untuk
                    Tipe Diklat: <strong>{{ $diklatTypeName ?? 'N/A' }}</strong>.
                </p>
                <ul class="list-group list-group-flush" style="font-size: 0.85rem;">
                    @forelse ($indikatorDampakRules ?? [] as $rule)
                        @php
                            $bgColorInfo = 'bg-secondary'; // Default
                            $textColorInfo = 'text-white';
                            if (isset($rule->kriteria_dampak)) {
                                if (Str::contains($rule->kriteria_dampak, 'Tidak Berdampak', true)) {
                                    $bgColorInfo = 'bg-danger';
                                    $textColorInfo = 'text-white';
                                } elseif (Str::contains($rule->kriteria_dampak, 'Kurang Berdampak', true)) {
                                    $bgColorInfo = 'bg-warning';
                                    $textColorInfo = 'text-dark';
                                } elseif (Str::contains($rule->kriteria_dampak, 'Cukup Berdampak', true)) {
                                    $bgColorInfo = 'bg-info';
                                    $textColorInfo = 'text-dark';
                                } elseif (Str::contains($rule->kriteria_dampak, 'Sangat Berdampak', true)) {
                                    $bgColorInfo = 'bg-success';
                                    $textColorInfo = 'text-white';
                                }
                            }
                        @endphp
                        <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-0">
                            <span>> {{ number_format($rule->nilai_minimal ?? 0, 0) }} &nbsp; s/d &nbsp; <=
                                    {{ number_format($rule->nilai_maksimal ?? 0, 0) }}</span>
                                    <span
                                        class="badge rounded-pill {{ $bgColorInfo }} {{ $textColorInfo }}">{{ $rule->kriteria_dampak ?? 'N/A' }}</span>
                        </li>
                    @empty
                        <li class="list-group-item py-1 px-0 fst-italic">Aturan predikat untuk tipe diklat ini belum
                            ditentukan.</li>
                    @endforelse
                </ul>
                <small class="text-muted mt-2 fst-italic d-block" style="font-size: 0.8rem;">
                    Logika Penentuan: Skor > Nilai Minimal DAN Skor <= Nilai Maksimal. <br>
                        Contoh: Jika aturan "Tidak Berdampak" 0-25 dan "Kurang B." 25-50, maka Skor 25 masuk ke "Tidak
                        Berdampak".
                </small>
            </div>
        </div>
        {{-- Akhir Informasi Kriteria Predikat --}}


        <div class="accordion" id="accordionEvaluasi">

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingLevel3">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseLevel3" aria-expanded="true" aria-controls="collapseLevel3">
                        Hasil Evaluasi Level 3 (Total Skor: <b>{{ number_format($totalLevel3 ?? 0, 2) }}</b>)
                    </button>
                </h2>
                <div id="collapseLevel3" class="accordion-collapse collapse show" aria-labelledby="headingLevel3"
                    data-bs-parent="#accordionEvaluasi">
                    <div class="accordion-body">
                        {{-- Bagian Tabel Level 3 --}}
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aspek</th>
                                        <th>Kriteria</th>
                                        <th>Rata-rata Skor</th>
                                        <th>Konversi</th>
                                        <th>Bobot</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data Alumni Level 3 --}}
                                    @if (!empty($detailAlumniLevel3))
                                        <tr class="table-secondary text-start fw-bold">
                                            <td colspan="6">Alumni</td>
                                        </tr>
                                        @foreach ($detailAlumniLevel3 as $item)
                                            <tr>
                                                <td class="text-start">{{ $item['aspek_nama'] ?? '-' }}</td>
                                                <td>{{ $item['kriteria'] ?? '-' }}</td>
                                                <td>{{ $item['average_nilai_delta'] ?? '-' }}</td>
                                                <td>{{ $item['konversi'] ?? '-' }}</td>
                                                <td>{{ isset($item['bobot']) ? number_format($item['bobot'], 2) : '-' }}%
                                                </td>
                                                <td>{{ isset($item['nilai']) ? number_format($item['nilai'], 2) : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="fw-bold table-light">
                                            <td colspan="5" class="text-end">Sub Total Nilai Alumni</td>
                                            <td>{{ number_format($skorData->skor_level_3_alumni ?? 0, 2) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center fst-italic text-muted">Data Alumni
                                                Level 3 tidak tersedia.</td>
                                        </tr>
                                    @endif

                                    {{-- Data Atasan Level 3 --}}
                                    @if (!empty($detailAtasanLevel3))
                                        <tr class="table-secondary text-start fw-bold">
                                            <td colspan="6">Atasan</td>
                                        </tr>
                                        @foreach ($detailAtasanLevel3 as $item)
                                            <tr>
                                                <td class="text-start">{{ $item['aspek_nama'] ?? '-' }}</td>
                                                <td>{{ $item['kriteria'] ?? '-' }}</td>
                                                <td>{{ $item['average_nilai_delta'] ?? '-' }}</td>
                                                <td>{{ $item['konversi'] ?? '-' }}</td>
                                                <td>{{ isset($item['bobot']) ? number_format($item['bobot'], 2) : '-' }}%
                                                </td>
                                                <td>{{ isset($item['nilai']) ? number_format($item['nilai'], 2) : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="fw-bold table-light">
                                            <td colspan="5" class="text-end">Sub Total Nilai Atasan</td>
                                            <td>{{ number_format($skorData->skor_level_3_atasan ?? 0, 2) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center fst-italic text-muted">Data Atasan
                                                Level 3 tidak tersedia.</td>
                                        </tr>
                                    @endif

                                    {{-- Total Keseluruhan Level 3 --}}
                                    <tr class="table-primary fw-bold"> {{-- Warna berbeda untuk total --}}
                                        <td colspan="5" class="text-end">TOTAL NILAI LEVEL 3</td>
                                        <td>{{ number_format($totalLevel3 ?? 0, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Akhir Tabel Level 3 --}}

                        {{-- Predikat Level 3 (Colorized) --}}
                        @php
                            $bgColorL3 = 'bg-secondary'; // Default
                            $textColorL3 = 'text-white';
                            $borderColorL3 = '#6c757d'; // Secondary color

                            if (isset($predikatLevel3)) {
                                if (Str::contains($predikatLevel3, 'Tidak Berdampak', true)) {
                                    $bgColorL3 = 'bg-danger';
                                    $textColorL3 = 'text-white';
                                    $borderColorL3 = '#dc3545'; // Merah
                                } elseif (Str::contains($predikatLevel3, 'Kurang Berdampak', true)) {
                                    $bgColorL3 = 'bg-warning';
                                    $textColorL3 = 'text-dark';
                                    $borderColorL3 = '#ffc107'; // Oranye
                                } elseif (Str::contains($predikatLevel3, 'Cukup Berdampak', true)) {
                                    $bgColorL3 = 'bg-info';
                                    $textColorL3 = 'text-dark';
                                    $borderColorL3 = '#0dcaf0'; // Kuning (pakai info)
                                } elseif (Str::contains($predikatLevel3, 'Sangat Berdampak', true)) {
                                    $bgColorL3 = 'bg-success';
                                    $textColorL3 = 'text-white';
                                    $borderColorL3 = '#198754'; // Hijau
                                } elseif (
                                    $predikatLevel3 === 'Kriteria Predikat Tidak Ditemukan' ||
                                    $predikatLevel3 === 'Data Skor Belum Tersedia'
                                ) {
                                    $bgColorL3 = 'bg-light';
                                    $textColorL3 = 'text-muted';
                                    $borderColorL3 = '#adb5bd'; // Abu-abu muda
                                }
                            }
                        @endphp
                        <div class="card mt-4 shadow-sm"
                            style="border-left: 5px solid {{ $borderColorL3 }}; border-radius: 8px;">
                            <div class="card-body text-center p-3 {{ $bgColorL3 }} {{ $textColorL3 }}"
                                style="border-radius: 0 8px 8px 0;">
                                <h6 class="card-subtitle mb-2 {{ $textColorL3 == 'text-dark' ? 'text-muted' : 'text-white-50' }}"
                                    style="font-size: 0.9rem;">Predikat Hasil Evaluasi Level 3</h6>
                                <p class="card-text fs-4 fw-bold mb-0 {{ $textColorL3 }}" style="line-height: 1.2;">
                                    {{ $predikatLevel3 ?? 'Belum Ada Predikat' }}
                                </p>
                            </div>
                        </div>
                        {{-- Akhir Predikat Level 3 --}}
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingLevel4">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseLevel4" aria-expanded="false" aria-controls="collapseLevel4">
                        Hasil Evaluasi Level 4 (Total Skor: <b>{{ number_format($totalLevel4 ?? 0, 2) }}</b>)
                    </button>
                </h2>
                <div id="collapseLevel4" class="accordion-collapse collapse" aria-labelledby="headingLevel4"
                    data-bs-parent="#accordionEvaluasi">
                    <div class="accordion-body">
                        {{-- Bagian Tabel Level 4 --}}
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aspek</th>
                                        <th>Kriteria</th>
                                        <th>Rata-rata Skor</th>
                                        <th>Konversi</th>
                                        <th>Bobot</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data Alumni Level 4 --}}
                                    @if (!empty($detailAlumniLevel4))
                                        <tr class="table-secondary text-start fw-bold">
                                            <td colspan="6">Alumni</td>
                                        </tr>
                                        @foreach ($detailAlumniLevel4 as $item)
                                            <tr>
                                                <td class="text-start">{{ $item['aspek_nama'] ?? '-' }}</td>
                                                <td>{{ $item['kriteria'] ?? '-' }}</td>
                                                <td>{{ $item['average_nilai_delta'] ?? '-' }}</td>
                                                <td>{{ $item['konversi'] ?? '-' }}</td>
                                                <td>{{ isset($item['bobot']) ? number_format($item['bobot'], 2) : '-' }}%
                                                </td>
                                                <td>{{ isset($item['nilai']) ? number_format($item['nilai'], 2) : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="fw-bold table-light">
                                            <td colspan="5" class="text-end">Sub Total Nilai Alumni</td>
                                            <td>{{ number_format($skorData->skor_level_4_alumni ?? 0, 2) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center fst-italic text-muted">Data Alumni
                                                Level 4 tidak tersedia.</td>
                                        </tr>
                                    @endif

                                    {{-- Data Atasan Level 4 --}}
                                    @if (!empty($detailAtasanLevel4))
                                        <tr class="table-secondary text-start fw-bold">
                                            <td colspan="6">Atasan</td>
                                        </tr>
                                        @foreach ($detailAtasanLevel4 as $item)
                                            <tr>
                                                <td class="text-start">{{ $item['aspek_nama'] ?? '-' }}</td>
                                                <td>{{ $item['kriteria'] ?? '-' }}</td>
                                                <td>{{ $item['average_nilai_delta'] ?? '-' }}</td>
                                                <td>{{ $item['konversi'] ?? '-' }}</td>
                                                <td>{{ isset($item['bobot']) ? number_format($item['bobot'], 2) : '-' }}%
                                                </td>
                                                <td>{{ isset($item['nilai']) ? number_format($item['nilai'], 2) : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="fw-bold table-light">
                                            <td colspan="5" class="text-end">Sub Total Nilai Atasan</td>
                                            <td>{{ number_format($skorData->skor_level_4_atasan ?? 0, 2) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center fst-italic text-muted">Data Atasan
                                                Level 4 tidak tersedia.</td>
                                        </tr>
                                    @endif

                                    {{-- Data Sekunder --}}
                                    <tr class="table-light fw-bold">
                                        <td colspan="5" class="text-end">Data Sekunder</td>
                                        <td>{{ number_format($nilaiSekunder ?? 0, 2) }}</td>
                                    </tr>

                                    {{-- Total Keseluruhan Level 4 --}}
                                    <tr class="table-primary fw-bold"> {{-- Warna berbeda untuk total --}}
                                        <td colspan="5" class="text-end">TOTAL NILAI LEVEL 4</td>
                                        <td>{{ number_format($totalLevel4 ?? 0, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Akhir Tabel Level 4 --}}

                        {{-- Predikat Level 4 (Colorized) --}}
                        @php
                            $bgColorL4 = 'bg-secondary'; // Default
                            $textColorL4 = 'text-white';
                            $borderColorL4 = '#6c757d'; // Secondary color

                            if (isset($predikatLevel4)) {
                                if (Str::contains($predikatLevel4, 'Tidak Berdampak', true)) {
                                    $bgColorL4 = 'bg-danger';
                                    $textColorL4 = 'text-white';
                                    $borderColorL4 = '#dc3545'; // Merah
                                } elseif (Str::contains($predikatLevel4, 'Kurang Berdampak', true)) {
                                    $bgColorL4 = 'bg-warning';
                                    $textColorL4 = 'text-dark';
                                    $borderColorL4 = '#ffc107'; // Oranye
                                } elseif (Str::contains($predikatLevel4, 'Cukup Berdampak', true)) {
                                    $bgColorL4 = 'bg-info';
                                    $textColorL4 = 'text-dark';
                                    $borderColorL4 = '#0dcaf0'; // Kuning (pakai info)
                                } elseif (Str::contains($predikatLevel4, 'Sangat Berdampak', true)) {
                                    $bgColorL4 = 'bg-success';
                                    $textColorL4 = 'text-white';
                                    $borderColorL4 = '#198754'; // Hijau
                                } elseif (
                                    $predikatLevel4 === 'Kriteria Predikat Tidak Ditemukan' ||
                                    $predikatLevel4 === 'Data Skor Belum Tersedia'
                                ) {
                                    $bgColorL4 = 'bg-light';
                                    $textColorL4 = 'text-muted';
                                    $borderColorL4 = '#adb5bd'; // Abu-abu muda
                                }
                            }
                        @endphp
                        <div class="card mt-4 shadow-sm"
                            style="border-left: 5px solid {{ $borderColorL4 }}; border-radius: 8px;">
                            <div class="card-body text-center p-3 {{ $bgColorL4 }} {{ $textColorL4 }}"
                                style="border-radius: 0 8px 8px 0;">
                                <h6 class="card-subtitle mb-2 {{ $textColorL4 == 'text-dark' ? 'text-muted' : 'text-white-50' }}"
                                    style="font-size: 0.9rem;">Predikat Hasil Evaluasi Level 4</h6>
                                <p class="card-text fs-4 fw-bold mb-0 {{ $textColorL4 }}" style="line-height: 1.2;">
                                    {{ $predikatLevel4 ?? 'Belum Ada Predikat' }}
                                </p>
                            </div>
                        </div>
                        {{-- Akhir Predikat Level 4 --}}
                    </div>
                </div>
            </div>
            {{-- Akhir Accordion Level 4 --}}

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Font Awesome JS (jika diperlukan untuk ikon dinamis, tapi biasanya CSS cukup) --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script> --}}
</body>

</html>
