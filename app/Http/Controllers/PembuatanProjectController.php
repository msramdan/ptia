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
        // Ambil data dari API sumber (seperti sebelumnya)
        $response = \Http::get("http://192.168.10.36:8090/api/len-kaldik", [
            'api_key' => '797e9aa1-be97-4dc0-ae13-3ecd304a61a3',
            'limit' => $request->limit,
            'page' => $request->page,
            'search' => $request->search
        ]);

        // Ambil data yang sudah di-fetch dari API
        $data = $response->json()['data'];

        // Ambil semua KaldikID dari data yang ada
        $kaldikIDs = collect($data)->pluck('kaldikID')->toArray();

        // Cek status generate di database dengan KaldikID
        $existingProjects = DB::table('project')
            ->whereIn('kaldikID', $kaldikIDs)
            ->pluck('kaldikID')->toArray();

        // Tambahkan status generate ke setiap item
        $dataWithStatus = collect($data)->map(function ($item) use ($existingProjects) {
            $status = in_array($item['kaldikID'], $existingProjects) ? 'SUDAH' : 'BELUM';
            $item['status_generate'] = $status;
            return $item;
        });

        return response()->json([
            'total' => $response->json()['total'],
            'data' => $dataWithStatus,
        ]);
    }
}
