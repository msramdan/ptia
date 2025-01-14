<?php

namespace App\Http\Controllers;

use App\Models\IndikatorPersepsi;
use App\Http\Requests\IndikatorPersepsis\{StoreIndikatorPersepsiRequest, UpdateIndikatorPersepsiRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

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
            $indikatorPersepsis = IndikatorPersepsi::with(['aspek:id,level']);

            return DataTables::of($indikatorPersepsis)
                ->addColumn('aspek', function ($row) {
                    return $row?->aspek?->level ?? '';
                })->addColumn('action', 'indikator-persepsis.include.action')
                ->toJson();
        }

        return view('indikator-persepsi.index');
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
        $indikatorPersepsi->load(['aspek:id,level']);

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
