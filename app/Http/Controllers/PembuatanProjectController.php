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
        $apiUrl =config('services.pusdiklatwas.endpoint') . "/len-peserta-diklat/{$kaldikID}";
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



}
