<?php

namespace App\Http\Controllers;

use App\Models\IndikatorPersepsi;
use App\Http\Requests\indikatorPersepsi\{StoreIndikatorPersepsiRequest, UpdateIndikatorPersepsiRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;


class IndikatorPersepsiController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:indikator persepsi view', only: ['index', 'show']),
            new Middleware('permission:indikator persepsi create', only: ['create', 'store']),
            new Middleware('permission:indikator persepsi edit', only: ['edit', 'update']),
            new Middleware('permission:indikator persepsi delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $indikatorPersepsi = DB::table('indikator_persepsi')
                ->join('aspek', 'indikator_persepsi.aspek_id', '=', 'aspek.id')
                ->select([
                    'indikator_persepsi.id',
                    'aspek.aspek as aspek',
                    'indikator_persepsi.indikator_persepsi',
                    'indikator_persepsi.kriteria_persepsi',
                ]);

            return DataTables::of($indikatorPersepsi)
                ->addIndexColumn()
                ->addColumn('indikator_persepsi', function ($row) {
                    return '<span class="badge bg-danger">' . $row->indikator_persepsi . '</span>';
                })
                ->rawColumns(['indikator_persepsi', 'action'])
                ->addColumn('action', 'indikator-persepsi.include.action')
                ->toJson();
        }
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        return view('indikator-persepsi.index', compact('diklatTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('indikator-persepsi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIndikatorPersepsiRequest $request): RedirectResponse
    {

        IndikatorPersepsi::create($request->validated());

        return to_route('indikator-persepsi.index')->with('success', __('The indikator persepsi was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(IndikatorPersepsi $indikatorPersepsi): View
    {
        $indikatorPersepsi->load(['aspek:id,aspek']);

        return view('indikator-persepsi.show', compact('indikatorPersepsi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IndikatorPersepsi $indikatorPersepsi): View
    {
        $indikatorPersepsi->load(['aspek:id,level']);

        return view('indikator-persepsi.edit', compact('indikatorPersepsi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIndikatorPersepsiRequest $request, IndikatorPersepsi $indikatorPersepsi): RedirectResponse
    {

        $indikatorPersepsi->update($request->validated());

        return to_route('indikator-persepsi.index')->with('success', __('The indikator persepsi was updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IndikatorPersepsi $indikatorPersepsi): RedirectResponse
    {
        try {
            $indikatorPersepsi->delete();

            return to_route('indikator-persepsi.index')->with('success', __('The indikator persepsi was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('indikator-persepsi.index')->with('error', __("The indikator persepsi can't be deleted because it's related to another table."));
        }
    }
}
