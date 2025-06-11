@extends('layouts.app')

@section('title', __('Backup Database'))

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Backup Database</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Backup Database</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tindakan</h5>
                </div>
                <div class="card-body">
                    <p>Klik tombol di bawah untuk membuat file cadangan (backup) baru. Proses ini mungkin memerlukan
                        beberapa waktu.</p>
                    <form action="{{ route('backup.create') }}" method="POST" id="backup-form">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-plus-circle"></i> Buat Backup Baru
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Backup Tersedia</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nama File</th>
                                    <th>Ukuran</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($backups as $backup)
                                    <tr>
                                        <td>
                                            <i class="fa fa-database me-2"></i>
                                            {{ $backup['file_name'] }}
                                        </td>
                                        <td>
                                            @if (is_numeric($backup['file_size']))
                                                {{ number_format($backup['file_size'] / 1024, 2) }} KB
                                            @else
                                                Ukuran tidak valid
                                            @endif
                                        </td>
                                        <td>{{ date('d M Y, H:i:s', $backup['last_modified']) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('backup.download', $backup['file_name']) }}"
                                                class="btn btn-sm btn-success">
                                                <i class="fa fa-download"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada file backup yang tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // 1. Script untuk konfirmasi SweetAlert2 sebelum membuat backup
        document.getElementById('backup-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah form langsung dikirim

            Swal.fire({
                title: 'Konfirmasi Proses',
                text: "Apakah Anda yakin ingin membuat backup baru? Proses ini akan berjalan di latar belakang.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan pesan loading
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang membuat file backup, mohon tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    // Jika user menekan "Ya", kirim form-nya
                    e.target.submit();
                }
            })
        });

        // 2. Script untuk otomatis download setelah backup selesai dibuat
        @if (session('download_file'))
            document.addEventListener('DOMContentLoaded', function() {
                // Beri sedikit jeda agar user bisa melihat halaman refresh
                setTimeout(() => {
                    const fileName = "{{ session('download_file') }}";
                    const downloadUrl = "{{ route('backup.download', ['fileName' => 'PLACEHOLDER']) }}"
                        .replace('PLACEHOLDER', fileName);

                    // Buat link tersembunyi dan klik secara otomatis
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = downloadUrl;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);

                    // Tampilkan notifikasi bahwa download telah dimulai
                    Swal.fire({
                        title: 'Download Dimulai!',
                        text: `File ${fileName} sedang diunduh.`,
                        icon: 'success',
                        timer: 3500,
                        showConfirmButton: false
                    });

                }, 1000); // Jeda 1 detik
            });
        @endif
    </script>
@endpush
