<?php

namespace App\Http\Controllers;

use App\Models\PembuatanProject;
use App\Http\Requests\PembuatanProjects\{StorePembuatanProjectRequest, UpdatePembuatanProjectRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // 1. Tambahkan use statement untuk PDF Facade
use Carbon\Carbon;

class PembuatanProjectController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:pembuatan project view', only: ['index']),
            new Middleware('permission:pembuatan project create', only: ['create']),
        ];
    }

    public function index(): View|JsonResponse
    {
        return view('pembuatan-project.index');
    }

    public function getKaldikData(Request $request)
    {
        $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-kaldik";
        $apiToken = config('services.pusdiklatwas.api_token');

        $response = \Http::get($apiUrl, [
            'api_key' => $apiToken,
            'limit' => $request->limit,
            'page' => $request->page,
            'search' => $request->search
        ]);

        $data = $response->json()['data'] ?? [];

        $kaldikIDs = collect($data)->pluck('kaldikID')->toArray();

        $existingProjects = DB::table('project')
            ->whereIn('kaldikID', $kaldikIDs)
            ->pluck('kaldikID')->toArray();

        $dataWithStatus = collect($data)->map(function ($item) use ($existingProjects) {
            $item['status_generate'] = in_array($item['kaldikID'], $existingProjects) ? 'SUDAH' : 'BELUM';
            return $item;
        });

        return response()->json([
            'total' => $response->json()['total'] ?? 0,
            'data' => $dataWithStatus,
        ]);
    }

    public function getDetail($kaldikID)
    {
        $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-kaldik/{$kaldikID}";
        $apiToken = config('services.pusdiklatwas.api_token');

        $response = \Http::get($apiUrl, [
            'api_key' => $apiToken
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'message' => 'Gagal mengambil data dari API'
        ], $response->status());
    }

    public function getPeserta(Request $request, $kaldikID)
    {
        // Get the necessary parameters from the request
        $limit = $request->input('limit', 10); // Default limit is 10 if not provided
        $page = $request->input('page', 1); // Default page is 1 if not provided
        $search = $request->input('search', ''); // Default search query is an empty string if not provided

        // Prepare the API URL and Token
        $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-peserta-diklat/{$kaldikID}";
        $apiToken = config('services.pusdiklatwas.api_token');

        // Make the API request
        $response = \Http::get($apiUrl, [
            'api_key' => $apiToken,
            'is_pagination' => 'Yes',
            'limit' => $limit,
            'page' => $page,
            'search' => $search
        ]);

        // Check if the response is successful
        if ($response->successful()) {
            $data = $response->json();

            // Return the data in the format that DataTables expects
            return response()->json([
                'data' => $data['data'], // The actual data to display in DataTable
                'recordsTotal' => $data['total'], // Total number of records (without filtering)
                'recordsFiltered' => $data['total'], // Total number of records (after filtering)
                'draw' => $request->input('draw') // DataTable draw counter (to prevent errors)
            ]);
        }

        // If the request fails, return an error response
        return response()->json([
            'message' => 'Gagal mengambil data dari API'
        ], $response->status());
    }

    /**
     * Export project details to PDF.
     *
     * @param  int  $id The Project ID
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($id) // 3. Definisikan method exportPdf
    {
        try {
            // 4. Ambil data project utama
            $project = DB::table('project')
                ->join('users', 'project.user_id', '=', 'users.id')
                ->join('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
                ->select(
                    'project.*',
                    'users.name as user_name', // Nama pembuat
                    'diklat_type.nama_diklat_type'
                )
                ->where('project.id', $id)
                ->first();

            if (!$project) {
                // Handle jika project tidak ditemukan
                return redirect()->route('project.index')->with('error', 'Project tidak ditemukan.');
            }

            // 5. Ambil data terkait lainnya (sesuaikan dengan kebutuhan PDF Anda)
            //    Contoh mengambil data dari file export-persiapan-pdf.blade.php:

            // Data Kuesioner Alumni
            $kuesionerAlumni = DB::table('project_kuesioner')
                ->join('aspek', 'project_kuesioner.aspek_id', '=', 'aspek.id')
                ->select('project_kuesioner.*', 'aspek.aspek as aspek_nama')
                ->where('project_kuesioner.project_id', $id)
                ->where('project_kuesioner.remark', 'Alumni')
                ->orderBy('project_kuesioner.id') // Urutkan jika perlu
                ->get();

            // Data Kuesioner Atasan
            $kuesionerAtasan = DB::table('project_kuesioner')
                ->join('aspek', 'project_kuesioner.aspek_id', '=', 'aspek.id')
                ->select('project_kuesioner.*', 'aspek.aspek as aspek_nama')
                ->where('project_kuesioner.project_id', $id)
                ->where('project_kuesioner.remark', 'Atasan')
                ->orderBy('project_kuesioner.id') // Urutkan jika perlu
                ->get();

            // Data Template Pesan WA
            $pesanWa = DB::table('project_pesan_wa')
                ->where('project_id', $id)
                ->first();
            $templateAlumni = $pesanWa ? $pesanWa->text_pesan_alumni : 'Template pesan alumni tidak ditemukan.';
            $templateAtasan = $pesanWa ? $pesanWa->text_pesan_atasan : 'Template pesan atasan tidak ditemukan.';

            // Data Progress Keterisian (Contoh, sesuaikan dengan logika Anda)
            // Anda perlu menambahkan logika untuk menghitung ini jika belum ada
            $totalAlumni = DB::table('project_responden')->where('project_id', $id)->count();
            // Contoh: Hitung yang sudah mengisi kuesioner alumni (perlu kolom status pengisian)
            $mengisiAlumni = DB::table('jawaban_responden_alumni')
                ->where('project_id', $id)
                ->distinct('responden_id')
                ->count('responden_id'); // Ganti dengan logika Anda yang benar
            $persenAlumni = ($totalAlumni > 0) ? ($mengisiAlumni / $totalAlumni) * 100 : 0;

            // Contoh: Hitung atasan (perlu data atasan dan status pengisian)
            // $totalAtasan = ...; // Logika Anda
            // $mengisiAtasan = ...; // Logika Anda
            // $persenAtasan = ...; // Logika Anda
            // Untuk sementara, kita beri nilai dummy
            $totalAtasan = 0;
            $mengisiAtasan = 0;
            $persenAtasan = 0;


            // Data Tambahan (seperti logo, tanggal cetak)
            $namaPembuat = $project->user_name ?? 'N/A';
            $tanggalCetak = Carbon::now()->translatedFormat('d F Y H:i'); // Format tanggal Indonesia
            // Ambil path logo (misalnya dari settings atau config)
            $setting = Setting::first();
            $logoPath = $setting && $setting->logo_instansi ? public_path('storage/uploads/logos/' . $setting->logo_instansi) : null;
            $logoUrl = $logoPath && file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;


            // 6. Siapkan data untuk dikirim ke view
            $data = [
                'project' => $project,
                'kuesionerAlumni' => $kuesionerAlumni,
                'kuesionerAtasan' => $kuesionerAtasan,
                'templateAlumni' => $templateAlumni,
                'templateAtasan' => $templateAtasan,
                'totalAlumni' => $totalAlumni,
                'mengisiAlumni' => $mengisiAlumni,
                'persenAlumni' => $persenAlumni,
                'totalAtasan' => $totalAtasan, // Ganti dengan data asli
                'mengisiAtasan' => $mengisiAtasan, // Ganti dengan data asli
                'persenAtasan' => $persenAtasan, // Ganti dengan data asli
                'namaPembuat' => $namaPembuat,
                'tanggalCetak' => $tanggalCetak,
                'logoUrl' => $logoUrl
            ];

            // 7. Generate PDF
            // Nama view blade yang akan kita buat nanti (misal: project.export-pdf)
            $pdf = Pdf::loadView('project.export-pdf', $data);

            // 8. Atur nama file PDF yang akan diunduh
            $filename = 'Persiapan-Evaluasi-' . Str::slug($project->kaldikDesc ?? 'project') . '-' . $project->kaldikID . '.pdf';

            // 9. Tawarkan PDF untuk diunduh
            return $pdf->download($filename);
        } catch (\Exception $e) {
            // Handle error jika terjadi kesalahan saat generate PDF
            \Log::error('Error generating PDF for project ID ' . $id . ': ' . $e->getMessage());
            return redirect()->route('project.index')->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
}
