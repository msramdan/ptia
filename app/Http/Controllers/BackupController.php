<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Exception;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:backup database view', only: ['index'])
        ];
    }

    public function index()
    {
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $backupName = config('backup.backup.name');
            $files = $disk->files($backupName);

            $backups = collect($files)
                ->filter(fn($file) => Str::endsWith($file, '.zip'))
                ->map(function ($file) use ($disk) {
                    return [
                        'file_name' => basename($file),
                        'file_path' => $file,
                        'file_size' => $disk->size($file),
                        'last_modified' => $disk->lastModified($file),
                    ];
                })
                ->sortByDesc('last_modified');

            return view('backup.index', compact('backups'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengakses direktori backup: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            set_time_limit(300);

            Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true,
            ]);

            $output = Artisan::output();

            \Log::info('Backup Output: ' . $output); // Log output

            if (Str::contains($output, 'Backup completed!')) {
                return redirect()->route('backup.index')
                    ->with('success', 'Backup database berhasil dibuat!');
            }

            return redirect()->route('backup.index')
                ->with('error', 'Backup gagal: ' . $output);
        } catch (Exception $e) {
            \Log::error('Backup Error: ' . $e->getMessage());
            return redirect()->route('backup.index')
                ->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function download($fileName)
    {
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
            $backupName = config('backup.backup.name');
            $filePath = $backupName . '/' . $fileName;

            if (!$disk->exists($filePath)) {
                throw new Exception('File backup tidak ditemukan.');
            }

            return $disk->download($filePath);
        } catch (Exception $e) {
            return redirect()->route('backup.index')->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }

    public function clean()
    {
        try {
            Artisan::call('backup:clean', [
                '--disable-notifications' => true,
            ]);

            $output = Artisan::output();

            if (Str::contains($output, 'Cleanup completed!')) {
                return redirect()->route('backup.index')->with('success', 'Pembersihan backup lama berhasil!');
            }

            return redirect()->route('backup.index')->with('error', 'Pembersihan gagal: ' . $output);
        } catch (Exception $e) {
            return redirect()->route('backup.index')->with('error', 'Gagal membersihkan backup lama: ' . $e->getMessage());
        }
    }
}
