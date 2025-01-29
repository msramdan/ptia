<?php

namespace App\Http\Controllers;

use App\Models\Konversi;
use App\Http\Requests\Konversis\{StoreKonversiRequest, UpdateKonversiRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class KonversiController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:konversi view', only: ['index', 'show']),
            new Middleware('permission:konversi create', only: ['create', 'store']),
            new Middleware('permission:konversi edit', only: ['edit', 'update']),
            new Middleware('permission:konversi delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $konversis = Konversi::query();

            return DataTables::of($konversis)
                ->addColumn('action', 'konversi.include.action')
                ->toJson();
        }

        return view('konversi.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('konversi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKonversiRequest $request): RedirectResponse
    {

        Konversi::create($request->validated());

        return to_route('konversi.index')->with('success', __('The konversi was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Konversi $konversi): View
    {
        return view('konversi.show', compact('konversi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Konversi $konversi): View
    {
        return view('konversi.edit', compact('konversi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKonversiRequest $request, Konversi $konversi): RedirectResponse
    {

        $konversi->update($request->validated());

        return to_route('konversi.index')->with('success', __('The konversi was updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Konversi $konversi): RedirectResponse
    {
        try {
            $konversi->delete();

            return to_route('konversi.index')->with('success', __('The konversi was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('konversi.index')->with('error', __("The konversi can't be deleted because it's related to another table."));
        }
    }
}
