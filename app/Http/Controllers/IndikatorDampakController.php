<?php

namespace App\Http\Controllers;

use App\Models\IndikatorDampak;
use App\Http\Requests\IndikatorDampaks\{StoreIndikatorDampakRequest, UpdateIndikatorDampakRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;

class IndikatorDampakController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:indikator dampak view', only: ['index', 'show']),
            new Middleware('permission:indikator dampak create', only: ['create', 'store']),
            new Middleware('permission:indikator dampak edit', only: ['edit', 'update']),
            new Middleware('permission:indikator dampak delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $indikatorDampaks = DB::table('indikator_dampak')
                ->join('diklat_type', 'indikator_dampak.diklat_type_id', '=', 'diklat_type.id')
                ->select('indikator_dampak.*', 'diklat_type.nama_diklat_type');

            // Tambahkan filter diklatType jika ada di request
            if (!empty(request()->diklatType)) {
                $indikatorDampaks->where('indikator_dampak.diklat_type_id', request()->diklatType);
            }

            return DataTables::of($indikatorDampaks)
                ->addIndexColumn()
                ->addColumn('indikator_dampak', function ($row) {
                    return $row->nilai_minimal . ' - ' . $row->nilai_maksimal;
                })
                ->addColumn('action', 'indikator-dampak.include.action')
                ->toJson();
        }

        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        $selectedDiklatType = request()->diklatType; // Ambil nilai filter dari URL

        return view('indikator-dampak.index', compact('diklatTypes', 'selectedDiklatType'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        return view('indikator-dampak.create', compact('diklatTypes'));
    }

    /**
     * Simpan data indikator dampak yang baru.
     */
    public function store(StoreIndikatorDampakRequest $request): RedirectResponse
    {
        IndikatorDampak::create($request->validated());

        return to_route('indikator-dampak.index')->with('success', __('Indikator dampak berhasil dibuat.'));
    }

    /**
     * Tampilkan detail indikator dampak.
     */
    public function show(IndikatorDampak $indikatorDampak): View
    {
        return view('indikator-dampak.show', compact('indikatorDampak'));
    }

    /**
     * Tampilkan formulir untuk mengedit indikator dampak.
     */
    public function edit(IndikatorDampak $indikatorDampak): View
    {
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        return view('indikator-dampak.edit', compact('indikatorDampak', 'diklatTypes'));
    }

    /**
     * Perbarui data indikator dampak yang dipilih.
     */
    public function update(UpdateIndikatorDampakRequest $request, IndikatorDampak $indikatorDampak): RedirectResponse
    {
        $indikatorDampak->update($request->validated());

        return to_route('indikator-dampak.index')->with('success', __('Indikator dampak berhasil diperbarui.'));
    }

    /**
     * Hapus data indikator dampak yang dipilih.
     */
    public function destroy(IndikatorDampak $indikatorDampak): RedirectResponse
    {
        try {
            $indikatorDampak->delete();

            return to_route('indikator-dampak.index')->with('success', __('Indikator dampak berhasil dihapus.'));
        } catch (\Exception $e) {
            return to_route('indikator-dampak.index')->with('error', __('Indikator dampak tidak dapat dihapus karena masih terhubung dengan tabel lain.'));
        }
    }
}
