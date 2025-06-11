<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Exception;

class BackupController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:backup database view', only: ['index', 'create', 'download']),
        ];
    }

    /**
     * Menampilkan halaman backup dengan daftar file yang sudah ada.
     */
    public function index()
    {
        $backups = collect();
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $files = $disk->files(config('app.name'));

            $backups = collect($files)
                ->map(function ($file) {
                    $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
                    return [
                        'file_path' => $file,
                        'file_name' => str_replace(config('app.name') . '/', '', $file),
                        'file_size' => $disk->size($file),
                        'last_modified' => $disk->lastModified($file),
                    ];
                })
                ->sortByDesc('last_modified');
        } catch (Exception $e) {
            session()->flash('error', 'Disk penyimpanan backup sepertinya belum terkonfigurasi. Cek file config/backup.php dan config/filesystems.php');
        }

        return view('backup.index', compact('backups'));
    }

    /**
     * Membuat file backup baru dan memicu unduhan di frontend.
     */
    public function create()
    {
        try {
            // Jalankan perintah backup seperti sebelumnya
            Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true,
            ]);

            // Ambil file terbaru yang baru saja dibuat
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $files = $disk->files(config('app.name'));

            $latestFile = collect($files)->sortByDesc(function ($file) use ($disk) {
                return $disk->lastModified($file);
            })->first();

            // Jika file ditemukan, kirim namanya kembali ke view untuk diunduh
            if ($latestFile) {
                $latestFileName = str_replace(config('app.name') . '/', '', $latestFile);

                return redirect()->route('backup.index')
                    ->with('success', 'File backup baru berhasil dibuat.')
                    ->with('download_file', $latestFileName); // "Pesan rahasia"
            }

            return back()->with('error', 'Backup berhasil dibuat, namun file tidak ditemukan.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh file backup yang sudah ada.
     */
    public function download(string $fileName)
    {
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $filePath = config('app.name') . '/' . $fileName;

            // Keamanan: pastikan file benar-benar ada sebelum diunduh
            if ($disk->exists($filePath)) {
                return $disk->download($filePath);
            }

            return back()->with('error', 'File backup tidak ditemukan.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }
}
