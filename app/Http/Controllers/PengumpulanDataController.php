<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanDatum;
use App\Http\Requests\PengumpulanDatas\{StorePengumpulanDatumRequest, UpdatePengumpulanDatumRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class PengumpulanDataController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:pengumpulan data view', only: ['index']),
        ];
    }

    public function index(): View|JsonResponse
    {
        return view('pengumpulan-data.index');
    }

}
