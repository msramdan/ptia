<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
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
        // Ambil daftar jenis diklat unik dari tabel diklat_type_mapping
        $jenisDiklatList = DB::table('diklat_type_mapping')
            ->select('diklatTypeName')
            ->distinct()
            ->orderBy('diklatTypeName')
            ->pluck('diklatTypeName');

        return view('pembuatan-project.index', compact('jenisDiklatList'));
    }

    public function getKaldikData(Request $request)
    {
        $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-kaldik";
        $apiToken = config('services.pusdiklatwas.api_token');
        $selectedJenisDiklat = $request->input('jenis_diklat');

        $apiParams = [
            'api_key' => $apiToken,
            'limit' => $request->limit,
            'page' => $request->page,
            'search' => $request->search
        ];

        $response = Http::get($apiUrl, $apiParams);

        $data = $response->json()['data'] ?? [];
        $total = $response->json()['total'] ?? 0;

        // Filter data berdasarkan jenis_diklat jika dipilih dan API tidak mendukung filter langsung
        if ($selectedJenisDiklat && !$request->has('diklatTypeName')) {
            $filteredData = [];
            foreach ($data as $item) {
                if (isset($item['diklatTypeName']) && $item['diklatTypeName'] == $selectedJenisDiklat) {
                    $filteredData[] = $item;
                }
            }
            $data = $filteredData;
            $total = count($data);
        }

        $kaldikIDs = collect($data)->pluck('kaldikID')->toArray();

        $existingProjects = DB::table('project')
            ->whereIn('kaldikID', $kaldikIDs)
            ->pluck('kaldikID')->toArray();

        $dataWithStatus = collect($data)->map(function ($item) use ($existingProjects) {
            $item['status_generate'] = in_array($item['kaldikID'], $existingProjects) ? 'SUDAH' : 'BELUM';
            return $item;
        });

        return response()->json([
            'total' => $total,
            'data' => $dataWithStatus,
        ]);
    }

    public function getDetail($kaldikID)
    {
        $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-kaldik/{$kaldikID}";
        $apiToken = config('services.pusdiklatwas.api_token');

        $response = Http::get($apiUrl, [
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
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');

        $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-peserta-diklat/{$kaldikID}";
        $apiToken = config('services.pusdiklatwas.api_token');

        $response = Http::get($apiUrl, [
            'api_key' => $apiToken,
            'is_pagination' => 'Yes',
            'limit' => $limit,
            'page' => $page,
            'search' => $search
        ]);

        if ($response->successful()) {
            $data = $response->json();

            return response()->json([
                'data' => $data['data'],
                'recordsTotal' => $data['total'],
                'recordsFiltered' => $data['total'],
                'draw' => $request->input('draw')
            ]);
        }

        return response()->json([
            'message' => 'Gagal mengambil data dari API'
        ], $response->status());
    }

    public function exportPdf($id)
    {
        try {
            $project = DB::table('project')
                ->join('users', 'project.user_id', '=', 'users.id')
                ->join('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
                ->select(
                    'project.*',
                    'users.name as user_name',
                    'diklat_type.nama_diklat_type'
                )
                ->where('project.id', $id)
                ->first();

            if (!$project) {
                return redirect()->route('project.index')->with('error', 'Project tidak ditemukan.');
            }

            $kuesionerAlumni = DB::table('project_kuesioner')
                ->join('aspek', 'project_kuesioner.aspek_id', '=', 'aspek.id')
                ->select('project_kuesioner.*', 'aspek.aspek as aspek_nama')
                ->where('project_kuesioner.project_id', $id)
                ->where('project_kuesioner.remark', 'Alumni')
                ->orderBy('project_kuesioner.id')
                ->get();

            $kuesionerAtasan = DB::table('project_kuesioner')
                ->join('aspek', 'project_kuesioner.aspek_id', '=', 'aspek.id')
                ->select('project_kuesioner.*', 'aspek.aspek as aspek_nama')
                ->where('project_kuesioner.project_id', $id)
                ->where('project_kuesioner.remark', 'Atasan')
                ->orderBy('project_kuesioner.id')
                ->get();

            $pesanWa = DB::table('project_pesan_wa')
                ->where('project_id', $id)
                ->first();
            $templateAlumni = $pesanWa ? $pesanWa->text_pesan_alumni : 'Template pesan alumni tidak ditemukan.';
            $templateAtasan = $pesanWa ? $pesanWa->text_pesan_atasan : 'Template pesan atasan tidak ditemukan.';

            $totalAlumni = DB::table('project_responden')->where('project_id', $id)->count();
            $mengisiAlumni = DB::table('jawaban_responden_alumni')
                ->where('project_id', $id)
                ->distinct('responden_id')
                ->count('responden_id');
            $persenAlumni = ($totalAlumni > 0) ? ($mengisiAlumni / $totalAlumni) * 100 : 0;

            $totalAtasan = 0;
            $mengisiAtasan = 0;
            $persenAtasan = 0;

            $namaPembuat = $project->user_name ?? 'N/A';
            $tanggalCetak = Carbon::now()->translatedFormat('d F Y H:i');
            $setting = Setting::first();
            $logoPath = $setting && $setting->logo_instansi ? public_path('storage/uploads/logos/' . $setting->logo_instansi) : null;
            $logoUrl = $logoPath && file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

            $data = [
                'project' => $project,
                'kuesionerAlumni' => $kuesionerAlumni,
                'kuesionerAtasan' => $kuesionerAtasan,
                'templateAlumni' => $templateAlumni,
                'templateAtasan' => $templateAtasan,
                'totalAlumni' => $totalAlumni,
                'mengisiAlumni' => $mengisiAlumni,
                'persenAlumni' => $persenAlumni,
                'totalAtasan' => $totalAtasan,
                'mengisiAtasan' => $mengisiAtasan,
                'persenAtasan' => $persenAtasan,
                'namaPembuat' => $namaPembuat,
                'tanggalCetak' => $tanggalCetak,
                'logoUrl' => $logoUrl
            ];

            $pdf = Pdf::loadView('project.export-pdf', $data);
            $filename = 'Persiapan-Evaluasi-' . Str::slug($project->kaldikDesc ?? 'project') . '-' . $project->kaldikID . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Error generating PDF for project ID ' . $id . ': ' . $e->getMessage());
            return redirect()->route('project.index')->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
}
