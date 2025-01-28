<?php

namespace App\Http\Controllers;

use App\Models\Aspek;
use App\Http\Requests\Aspeks\{StoreAspekRequest, UpdateAspekRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;

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

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $aspeks = Aspek::query();

            return DataTables::of($aspeks)
                ->addColumn('level', function ($row) {
                    $levelText = $row->level === '3' ? 'Level 3' : 'Level 4';
                    return '<span class="badge bg-info">' . $levelText . '</span>';
                })
                ->addColumn('action', 'aspek.include.action')
                ->rawColumns(['level', 'action'])
                ->toJson();
        }

        return view('aspek.index');
    }

    public function create(): View
    {
        return view('aspek.create');
    }

    public function store(StoreAspekRequest $request): RedirectResponse
    {
        $aspek = Aspek::create($request->validated());
        $indikatorPersepsi = [
            ['indikator_persepsi' => '1', 'kriteria_persepsi' => 'Sangat tidak setuju'],
            ['indikator_persepsi' => '2', 'kriteria_persepsi' => 'Tidak setuju'],
            ['indikator_persepsi' => '3', 'kriteria_persepsi' => 'Setuju'],
            ['indikator_persepsi' => '4', 'kriteria_persepsi' => 'Sangat setuju'],
        ];

        $data = [];
        foreach ($indikatorPersepsi as $indikator) {
            $data[] = [
                'aspek_id' => $aspek->id,
                'indikator_persepsi' => $indikator['indikator_persepsi'],
                'kriteria_persepsi' => $indikator['kriteria_persepsi'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('indikator_persepsi')->insert($data);
        return to_route('aspek.index')->with('success', __('The aspek was created successfully.'));
    }
    
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
