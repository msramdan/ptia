@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
    <style>
        body[data-bs-theme="light"] .gauge-title {
            color: #212529;
        }

        body[data-bs-theme="dark"] .gauge-title {
            color: #f8f9fa;
        }

        .heading-with-logo {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1rem 0;
        }

        .header-logo {
            height: 4.375rem;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 0.125rem 0.25rem rgba(0, 0, 0, 0.1));
        }

        .heading-text {
            border-left: 0.125rem solid #e74c3c;
            padding-left: 1.25rem;
        }

        .page-heading h5 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.03125rem;
        }

        .page-heading p {
            margin: 0.3125rem 0 0;
            color: #7f8c8d;
            font-size: 0.9375rem;
            font-weight: 500;
        }

        .gauge-container {
            position: relative;
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .gauge-header {
            margin-bottom: 1.25rem;
        }

        .gauge-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.3125rem;
            text-align: center;
        }

        .gauge-value-display {
            position: absolute;
            bottom: 30%;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 10;
        }

        .gauge-current-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.125rem;
            text-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
            line-height: 1.2;
        }

        .gauge-value-label {
            font-size: 0.875rem;
            /* 14px */
            color: #7f8c8d;
            font-weight: 500;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .gauge-current-value.updated {
            animation: pulse 0.5s ease-in-out;
        }

        @media (max-width: 48rem) {
            .heading-with-logo {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.9375rem;
            }

            .header-logo {
                height: 3.75rem;
            }

            .heading-text {
                border-left: none;
                padding-left: 0;
                border-top: 0.125rem solid #e74c3c;
                padding-top: 0.9375rem;
                width: 100%;
            }

            .gauge-title {
                font-size: 1rem;
            }

            .gauge-current-value {
                font-size: 1.5rem;
            }
        }
    </style>
    <style>
        .filter-icon {
            font-size: 1.25rem;
            color: #e74c3c;
        }

        .filter-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .year-selector {
            width: 120px;
        }

        .year-select {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .year-select:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 0.25rem rgba(231, 76, 60, 0.25);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .year-filter-container {
                max-width: 100%;
            }

            .year-selector {
                width: 100px;
            }
        }

        .btn-pdf-report {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            white-space: nowrap;
        }

        .btn-pdf-report:hover {
            background-color: #c0392b;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-pdf-report:active {
            transform: translateY(0);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .year-filter-container .card-body {
                padding: 0.75rem;
            }

            .year-selector {
                min-width: 100px;
            }

            .btn-pdf-report {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
                width: 100%;
                justify-content: center;
                margin-top: 0.5rem;
            }
        }

        .card-body i {
            transition: transform 0.3s ease;
        }

        .card:hover i {
            transform: scale(1.2);
        }

        .card-text {
            font-size: 1.1rem;
        }

        tfoot th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>

    <div class="page-heading">
        <div class="heading-with-logo">
            <img src="{{ asset('assets/BPKP_Logo.png') }}" alt="BPKP Logo" class="header-logo" style="width: 140px">
            <div class="heading-text">
                <h6>Badan Pengawasan Keuangan dan Pembangunan</h6>
                <p>PUSDIKLATWAS BPKP - Post Training Impact Assesment</p>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-md-12">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible show fade">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Modal Pengumuman -->
                <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel"
                    aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="announcementModalLabel">Pengumuman</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @php
                                    $setting = \App\Models\Setting::first();
                                @endphp
                                <p style="text-align: justify">{{ $setting->pengumuman }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-alt filter-icon me-2"></i>
                                                <h5 class="filter-title mb-0">Filter Tahun</h5>
                                                <div class="year-selector ms-3">
                                                    <select class="form-select year-select" aria-label="Pilih Tahun"
                                                        onchange="window.location.href='?tahun=' + this.value">
                                                        @foreach ([2025, 2026, 2027] as $thn)
                                                            <option value="{{ $thn }}"
                                                                {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- <button class="btn btn-pdf-report">
                                                <i class="fas fa-file-pdf me-1"></i> General Report
                                            </button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <i class="fas fa-folder-open fa-2x text-primary mb-2"></i>
                                        <h6 class="card-title">Total Project</h6>
                                        <p class="card-text fw-bold">{{ $jumlahProject }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <i class="fas fa-users fa-2x text-success mb-2"></i>
                                        <h6 class="card-title">Total Responden</h6>
                                        <p class="card-text fw-bold">{{ $jumlahResponden }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <i class="fas fa-user-graduate fa-2x text-warning mb-2"></i>
                                        <h6 class="card-title">Keterisian Alumni</h6>
                                        <p class="card-text fw-bold">({{ $sudahAlumni }}) - {{ $persentaseSudah }} %</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <i class="fas fa-user-tie fa-2x text-danger mb-2"></i>
                                        <h6 class="card-title">Keterisian Atasan</h6>
                                        <p class="card-text fw-bold">({{ $sudahAtasan }}) - {{ $persentaseSudahAtasan }}
                                            %</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body" style="min-height: 300px; max-height: 440px; overflow-y: hidden;">
                                <div class="gauge-container">
                                    <div class="gauge-header">
                                        <div class="gauge-title text-gray-900 dark:text-white">Skor Dampak Level 3</div>
                                    </div>
                                    <figure class="highcharts-figure">
                                        <div id="container"></div>
                                        <div class="gauge-value-display">
                                            <div class="gauge-current-value" id="current-value">0%</div>
                                            <div class="gauge-value-label">Skor Saat Ini</div>
                                        </div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body" style="min-height: 300px; max-height: 440px; overflow-y: hidden;">
                                <div class="gauge-container">
                                    <div class="gauge-header">
                                        <div class="gauge-title">Skor Dampak Level 4</div>
                                    </div>
                                    <figure class="highcharts-figure">
                                        <div id="container-level4"></div>
                                        <div class="gauge-value-display">
                                            <div class="gauge-current-value" id="current-value-level4">0%</div>
                                            <div class="gauge-value-label">Skor Saat Ini</div>
                                        </div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Table Data Hasil Evaluasi 2025</h6>
                                    <div class="table-responsive p-1">
                                        <table class="table table-striped" id="data-table" width="100%">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">#</th>
                                                    <th rowspan="2">{{ __('Evaluator') }}</th>
                                                    <th rowspan="2">{{ __('Kode Diklat') }}</th>
                                                    <th rowspan="2">{{ __('Nama Diklat') }}</th>
                                                    <th rowspan="2">{{ __('Jenis Diklat') }}</th>
                                                    <th colspan="2" class="text-center">{{ __('Level 3') }}</th>
                                                    <th colspan="2" class="text-center">{{ __('Level 4') }}</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">Skor</th>
                                                    <th class="text-center">Predikat</th>
                                                    <th class="text-center">Skor</th>
                                                    <th class="text-center">Predikat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="5" style="text-align:right"></th>
                                                    <th class="text-center"></th>
                                                    <th></th>
                                                    <th class="text-center"></th>
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
        </section>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('show_pengumuman'))
                var announcementModal = new bootstrap.Modal(document.getElementById('announcementModal'));
                announcementModal.show();
                @php
                    session()->forget('show_pengumuman');
                @endphp
            @endif
        });
    </script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.js"></script>
    <script>
        // Deklarasikan chart di level global agar bisa diakses dari mana saja
        var chartLevel3, chartLevel4;

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi gauge dengan nilai 0
            chartLevel3 = Highcharts.chart('container', {
                chart: {
                    type: 'gauge',
                    plotBackgroundColor: null,
                    plotBackgroundImage: null,
                    plotBorderWidth: 0,
                    plotShadow: false,
                    height: '80%',
                    backgroundColor: 'transparent'
                },
                exporting: {
                    enabled: false
                },
                title: {
                    text: null
                },
                credits: {
                    enabled: false
                },
                pane: {
                    startAngle: -90,
                    endAngle: 89.9,
                    background: null,
                    center: ['50%', '75%'],
                    size: '110%',
                    borderWidth: 0
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    tickPixelInterval: 72,
                    tickPosition: 'inside',
                    tickColor: '#f8f9fa',
                    tickLength: 15,
                    tickWidth: 2,
                    minorTickInterval: null,
                    labels: {
                        distance: 25,
                        style: {
                            fontSize: '12px',
                            color: '#95a5a6'
                        }
                    },
                    lineWidth: 0,
                    plotBands: [{
                            from: 0,
                            to: 25,
                            color: '#e74c3c',
                            thickness: 20,
                            borderRadius: 5
                        },
                        {
                            from: 25,
                            to: 50,
                            color: '#f39c12',
                            thickness: 20,
                            borderRadius: 5
                        },
                        {
                            from: 50,
                            to: 75,
                            color: '#3498db',
                            thickness: 20,
                            borderRadius: 5
                        },
                        {
                            from: 75,
                            to: 100,
                            color: '#2ecc71',
                            thickness: 20,
                            borderRadius: 5
                        }
                    ]
                },
                series: [{
                    name: 'Skor',
                    data: [0], // Nilai awal 0
                    tooltip: {
                        valueSuffix: ' %'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    dial: {
                        radius: '80%',
                        backgroundColor: '#34495e',
                        baseWidth: 12,
                        baseLength: '0%',
                        rearLength: '0%',
                        borderWidth: 1,
                        borderColor: '#fff'
                    },
                    pivot: {
                        backgroundColor: '#34495e',
                        radius: 6,
                        borderWidth: 1,
                        borderColor: '#fff'
                    }
                }]
            });

            chartLevel4 = Highcharts.chart('container-level4', {
                chart: {
                    type: 'gauge',
                    plotBackgroundColor: null,
                    plotBackgroundImage: null,
                    plotBorderWidth: 0,
                    plotShadow: false,
                    height: '80%',
                    backgroundColor: 'transparent'
                },
                exporting: {
                    enabled: false
                },
                title: {
                    text: null
                },
                credits: {
                    enabled: false
                },
                pane: {
                    startAngle: -90,
                    endAngle: 89.9,
                    background: null,
                    center: ['50%', '75%'],
                    size: '110%',
                    borderWidth: 0
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    tickPixelInterval: 72,
                    tickPosition: 'inside',
                    tickColor: '#f8f9fa',
                    tickLength: 15,
                    tickWidth: 2,
                    minorTickInterval: null,
                    labels: {
                        distance: 25,
                        style: {
                            fontSize: '12px',
                            color: '#95a5a6'
                        }
                    },
                    lineWidth: 0,
                    plotBands: [{
                            from: 0,
                            to: 25,
                            color: '#e74c3c',
                            thickness: 20,
                            borderRadius: 5
                        },
                        {
                            from: 25,
                            to: 50,
                            color: '#f39c12',
                            thickness: 20,
                            borderRadius: 5
                        },
                        {
                            from: 50,
                            to: 75,
                            color: '#3498db',
                            thickness: 20,
                            borderRadius: 5
                        },
                        {
                            from: 75,
                            to: 100,
                            color: '#2ecc71',
                            thickness: 20,
                            borderRadius: 5
                        }
                    ]
                },
                series: [{
                    name: 'Skor',
                    data: [0], // Nilai awal 0
                    tooltip: {
                        valueSuffix: ' %'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    dial: {
                        radius: '80%',
                        backgroundColor: '#34495e',
                        baseWidth: 12,
                        baseLength: '0%',
                        rearLength: '0%',
                        borderWidth: 1,
                        borderColor: '#fff'
                    },
                    pivot: {
                        backgroundColor: '#34495e',
                        radius: 6,
                        borderWidth: 1,
                        borderColor: '#fff'
                    }
                }]
            });
        });

        $(document).ready(function() {
            var tahun = "{{ $tahun }}";
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dashboard') }}",
                    data: function(d) {
                        d.tahun = tahun;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'kaldikID',
                        name: 'kaldikID'
                    },
                    {
                        data: 'nama_project',
                        name: 'nama_project'
                    },
                    {
                        data: 'nama_diklat_type',
                        name: 'nama_diklat_type'
                    },
                    {
                        data: 'avg_skor_level_3',
                        name: 'avg_skor_level_3'
                    },
                    {
                        data: 'kriteria_dampak_level_3',
                        name: 'kriteria_dampak_level_3'
                    },
                    {
                        data: 'avg_skor_level_4',
                        name: 'avg_skor_level_4'
                    },
                    {
                        data: 'kriteria_dampak_level_4',
                        name: 'kriteria_dampak_level_4'
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Mendapatkan data summary dari server
                    $.ajax({
                        url: "{{ route('dashboard') }}",
                        data: {
                            tahun: tahun,
                            draw: 1,
                            length: -1 // Untuk mendapatkan semua data
                        },
                        success: function(response) {
                            if (response && response.summary) {
                                var avgLevel3 = parseFloat(response.summary
                                    .avg_skor_level_3) || 0;
                                var avgLevel4 = parseFloat(response.summary
                                    .avg_skor_level_4) || 0;

                                // Update footer tabel
                                $(api.column(5).footer()).html(
                                    '<strong>' + avgLevel3.toFixed(2) + '</strong>'
                                );
                                $(api.column(7).footer()).html(
                                    '<strong>' + avgLevel4.toFixed(2) + '</strong>'
                                );

                                // Update gauge Level 3
                                if (chartLevel3) {
                                    chartLevel3.series[0].points[0].update(avgLevel3);
                                    document.getElementById('current-value').textContent =
                                        avgLevel3.toFixed(2) + '%';
                                }

                                // Update gauge Level 4
                                if (chartLevel4) {
                                    chartLevel4.series[0].points[0].update(avgLevel4);
                                    document.getElementById('current-value-level4')
                                        .textContent = avgLevel4.toFixed(2) + '%';
                                }
                            }
                        },
                        error: function() {
                            console.error('Gagal mengambil data rata-rata');
                        }
                    });
                },
                initComplete: function() {
                    // Inisialisasi footer
                    this.api().columns().every(function() {
                        var column = this;
                        if (column.index() === 5 || column.index() === 7) {
                            $(column.footer()).html('<strong>Loading...</strong>');
                        }
                    });
                }
            });
        });
    </script>
@endpush
