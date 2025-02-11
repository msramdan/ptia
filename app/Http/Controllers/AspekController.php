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
            $query = DB::table('aspek')
                ->join('diklat_type', 'aspek.diklat_type_id', '=', 'diklat_type.id')
                ->select('aspek.*', 'diklat_type.nama_diklat_type');

            if (!empty(request()->diklatType)) {
                $query->where('aspek.diklat_type_id', request()->diklatType);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('level', function ($row) {
                    $levelText = $row->level === '3' ? 'Level 3' : 'Level 4';
                    return '<span class="badge bg-info">' . $levelText . '</span>';
                })
                ->addColumn('action', 'aspek.include.action')
                ->rawColumns(['level', 'action'])
                ->toJson();
        }

        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        $selectedDiklatType = request()->diklatType; // Ambil filter dari URL

        return view('aspek.index', compact('diklatTypes', 'selectedDiklatType'));
    }

    public function create(): View
    {
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();

        return view('aspek.create', compact('diklatTypes'));
    }

    public function store(StoreAspekRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $aspek = Aspek::create($request->validated());

            $indikatorPersepsi = [
                ['indikator_persepsi' => '1', 'kriteria_persepsi' => 'Sangat tidak setuju'],
                ['indikator_persepsi' => '2', 'kriteria_persepsi' => 'Tidak setuju'],
                ['indikator_persepsi' => '3', 'kriteria_persepsi' => 'Setuju'],
                ['indikator_persepsi' => '4', 'kriteria_persepsi' => 'Sangat setuju'],
            ];

            $dataIndikator = [];
            foreach ($indikatorPersepsi as $indikator) {
                $dataIndikator[] = [
                    'aspek_id' => $aspek->id,
                    'indikator_persepsi' => $indikator['indikator_persepsi'],
                    'kriteria_persepsi' => $indikator['kriteria_persepsi'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('indikator_persepsi')->insert($dataIndikator);

            DB::table('bobot_aspek')->insert([
                'aspek_id' => $aspek->id,
                'bobot_alumni' => 0.0,
                'bobot_atasan_langsung' => 0.0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return to_route('aspek.index')->with('success', __('Aspek berhasil dibuat.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Gagal membuat aspek: ') . $e->getMessage());
        }
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
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        return view('aspek.edit', compact('aspek','diklatTypes'));
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
