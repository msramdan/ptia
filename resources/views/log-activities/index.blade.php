@extends('layouts.app')

@section('title', 'Log Aktivitas')

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
    <style>
        /* CSS untuk tombol expand/collapse */
        td.dt-control {
            background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.dt-hasChild td.dt-control {
            background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
        }

        /* Styling untuk baris detail */
        .details-container {
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
        }

        .details-table {
            width: 100%;
        }

        .details-table td {
            padding: 5px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }

        .details-label {
            font-weight: bold;
            width: 120px;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Log Aktivitas</h3>
                    <p class="text-subtitle text-muted">Rekaman semua aktivitas dalam sistem. Klik ikon '+' untuk melihat
                        detail.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Log Aktivitas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="log-activities-table" width="100%">
                            <thead>
                                <tr>
                                    <th width="5%"></th> {{-- Kolom untuk tombol expand/collapse --}}
                                    <th width="15%">Waktu</th>
                                    <th>Log</th>
                                    <th width="15%">User</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script src="{{ asset('mazer/extensions/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/combine/npm/datatables.net@1.12.0,npm/datatables.net-bs5@1.12.0"></script>

    <script>
        // Fungsi untuk memformat baris detail yang akan muncul saat di-klik
        function formatDetails(d) {
            let detailsHtml =
                '<div class="details-container"><table class="details-table" cellpadding="5" cellspacing="0" border="0">';

            // Menampilkan IP dan User Agent
            if (d.properties && d.properties.ip_address) {
                detailsHtml += `<tr><td class="details-label">IP Address:</td><td>${d.properties.ip_address}</td></tr>`;
            }
            if (d.properties && d.properties.user_agent) {
                detailsHtml += `<tr><td class="details-label">User Agent:</td><td>${d.properties.user_agent}</td></tr>`;
            }

            // Menampilkan Old dan New Values jika ada (hanya untuk event 'updated')
            if (d.properties && d.properties.old) {
                let oldValuesHtml = '';
                for (const key in d.properties.old) {
                    const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    oldValuesHtml +=
                    `<strong>${formattedKey}:</strong><br><code>${d.properties.old[key] || '-'}</code><br>`;
                }
                detailsHtml += `<tr><td class="details-label">Old Values:</td><td>${oldValuesHtml}</td></tr>`;
            }

            if (d.properties && d.properties.attributes) {
                let newValuesHtml = '';
                for (const key in d.properties.attributes) {
                    // Hanya tampilkan jika key ada di old values (untuk update) atau jika tidak ada old values sama sekali (untuk create)
                    if (!d.properties.old || d.properties.old.hasOwnProperty(key)) {
                        const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        newValuesHtml +=
                            `<strong>${formattedKey}:</strong><br><code>${d.properties.attributes[key] || '-'}</code><br>`;
                    }
                }
                detailsHtml += `<tr><td class="details-label">New Values:</td><td>${newValuesHtml}</td></tr>`;
            }

            detailsHtml += '</table></div>';
            return detailsHtml;
        }

        $(document).ready(function() {
            var table = $('#log-activities-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('log-activities.index') }}",
                order: [
                    [1, 'desc'] // Urutkan berdasarkan kolom Waktu (indeks ke-1)
                ],
                pageLength: 100,
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'causer.name',
                        name: 'causer.name',
                        defaultContent: 'System'
                    }
                ]
            });

            // Tambahkan event listener untuk membuka dan menutup detail
            $('#log-activities-table tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // Baris ini sudah terbuka, tutup
                    row.child.hide();
                    tr.removeClass('dt-hasChild');
                } else {
                    // Buka baris ini
                    row.child(formatDetails(row.data())).show();
                    tr.addClass('dt-hasChild');
                }
            });
        });
    </script>
@endpush
