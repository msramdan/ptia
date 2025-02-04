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
}
