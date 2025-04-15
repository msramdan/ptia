<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Terima Kasih</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Add gradient background with smooth animation */
        body {
            background: linear-gradient(45deg, #A2C2E5, #B4D8F3);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            height: 100vh;
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
            width: 100px;
            height: 100px;
            margin: auto;
            margin-bottom: 20px;
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
            font-size: 2rem;
        }

        .thank-you-text p {
            font-size: 1rem;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.3rem;
            font-size: 0.85rem;
        }

        .accordion-button {
            font-size: 1rem;
        }

        .container-sm {
            max-width: 800px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <br>
    <div class="container-sm text-center thank-you-text mb-4">
        <svg class="check-circle text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <circle cx="12" cy="12" r="10" stroke-width="2" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4 -4" />
        </svg>
        <h1 class="fw-bold text-success">Terima Kasih!</h1>
        <p class="text-muted">Kuesioner Anda telah berhasil dikirim. Berikut adalah hasil evaluasi Anda.</p>
    </div>

    <div class="container-sm">
        <div class="accordion" id="accordionEvaluasi">

            <!-- Accordion Item: Level 3 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingLevel3">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseLevel3" aria-expanded="true" aria-controls="collapseLevel3">
                        Hasil Evaluasi Level 3
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
                                        <tr class="fw-bold">
                                            <td colspan="5" class="text-end">Total Nilai Alumni</td>
                                            {{-- Ambil dari skorData jika ada --}}
                                            <td>{{ number_format($skorData->skor_level_3_alumni ?? 0, 2) }}</td>
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
                                        <tr class="fw-bold">
                                            <td colspan="5" class="text-end">Total Nilai Atasan</td>
                                            {{-- Ambil dari skorData jika ada --}}
                                            <td>{{ number_format($skorData->skor_level_3_atasan ?? 0, 2) }}</td>
                                        </tr>
                                    @endif

                                    {{-- Total Keseluruhan Level 3 --}}
                                    <tr class="table-light fw-bold">
                                        <td colspan="5" class="text-end">Total Nilai Alumni & Atasan</td>
                                        <td>{{ number_format($totalLevel3, 2) }}</td> {{-- Gunakan totalLevel3 --}}
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accordion Item: Level 4 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingLevel4">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseLevel4" aria-expanded="false" aria-controls="collapseLevel4">
                        Hasil Evaluasi Level 4
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
                                        <tr class="fw-bold">
                                            <td colspan="5" class="text-end">Total Nilai Alumni</td>
                                            {{-- Ambil dari skorData jika ada --}}
                                            <td>{{ number_format($skorData->skor_level_4_alumni ?? 0, 2) }}</td>
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
                                        <tr class="fw-bold">
                                            <td colspan="5" class="text-end">Total Nilai Atasan</td>
                                            {{-- Ambil dari skorData jika ada --}}
                                            <td>{{ number_format($skorData->skor_level_4_atasan ?? 0, 2) }}</td>
                                        </tr>
                                    @endif

                                    {{-- Data Sekunder --}}
                                    <tr class="table-light fw-bold">
                                        <td colspan="5" class="text-end">Data Sekunder</td>
                                        <td>{{ number_format($nilaiSekunder, 2) }}</td> {{-- Gunakan nilaiSekunder --}}
                                    </tr>

                                    {{-- Total Keseluruhan Level 4 --}}
                                    <tr class="table-light fw-bold">
                                        <td colspan="5" class="text-end">Total Nilai</td>
                                        <td>{{ number_format($totalLevel4, 2) }}</td> {{-- Gunakan totalLevel4 --}}
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
