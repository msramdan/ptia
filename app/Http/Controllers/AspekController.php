<?php

namespace App\Http\Controllers;

use App\Models\Aspek;
use App\Http\Requests\Aspeks\{StoreAspekRequest, UpdateAspekRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class AspekController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:aspek view', only: ['index', 'show']),
            new Middleware('permission:aspek create', only: ['create', 'store']),
            new Middleware('permission:aspek edit', only: ['edit', 'update']),
            new Middleware('permission:aspek delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $aspeks = Aspek::query();

            return DataTables::of($aspeks)
                ->addColumn('level', function ($user) {
                    $levelText = $user->level === '3' ? 'Level 3' : 'Level 4';
                    return '<span class="badge bg-info">' . $levelText . '</span>';
                })
                ->addColumn('action', 'aspek.include.action')
                ->rawColumns(['level', 'action'])
                ->toJson();
        }

        return view('aspek.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('aspek.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAspekRequest $request): RedirectResponse
    {

        Aspek::create($request->validated());

        return to_route('aspek.index')->with('success', __('The aspek was created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Aspek $aspek): View
    {
        return view('aspek.show', compact('aspek'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aspek $aspek): View
    {
        return view('aspek.edit', compact('aspek'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAspekRequest $request, Aspek $aspek): RedirectResponse
    {

        $aspek->update($request->validated());

        return to_route('aspek.index')->with('success', __('The aspek was updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aspek $aspek): RedirectResponse
    {
        try {
            $aspek->delete();

            return to_route('aspek.index')->with('success', __('The aspek was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('aspek.index')->with('error', __("The aspek can't be deleted because it's related to another table."));
        }
    }
}
