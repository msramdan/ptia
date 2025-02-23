<?php

namespace App\Http\Controllers;

use App\Models\Konversi;
use App\Http\Requests\Konversis\{StoreKonversiRequest, UpdateKonversiRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;

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
            $konversis = DB::table('konversi')
                ->join('diklat_type', 'konversi.diklat_type_id', '=', 'diklat_type.id')
                ->select([
                    'konversi.*',
                    'diklat_type.nama_diklat_type',
                ]);

            // Tambahkan filter diklatType jika ada di request
            if (!empty(request()->diklatType)) {
                $konversis->where('konversi.diklat_type_id', request()->diklatType);
            }

            return DataTables::of($konversis)
                ->addIndexColumn()
                ->addColumn('skor', function ($row) {
                    return '<span class="badge bg-danger">' . $row->skor . '</span>';
                })
                ->addColumn('konversi', function ($row) {
                    return $row->konversi . ' %';
                })
                ->addColumn('action', 'konversi.include.action')
                ->rawColumns(['skor', 'action'])
                ->toJson();
        }

        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        $selectedDiklatType = request()->diklatType; // Ambil nilai filter dari URL

        return view('konversi.index', compact('diklatTypes', 'selectedDiklatType'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        return view('konversi.create', compact('diklatTypes'));
    }

    /**
     * Simpan data konversi yang baru.
     */
    public function store(StoreKonversiRequest $request): RedirectResponse
    {
        Konversi::create($request->validated());

        return to_route('konversi.index')->with('success', __('Konversi berhasil dibuat.'));
    }

    /**
     * Tampilkan detail konversi.
     */
    public function show(Konversi $konversi): View
    {
        return view('konversi.show', compact('konversi'));
    }

    /**
     * Tampilkan formulir untuk mengedit konversi.
     */
    public function edit(Konversi $konversi): View
    {
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        return view('konversi.edit', compact('konversi', 'diklatTypes'));
    }

    /**
     * Perbarui data konversi yang dipilih.
     */
    public function update(UpdateKonversiRequest $request, Konversi $konversi): RedirectResponse
    {
        $konversi->update($request->validated());

        return to_route('konversi.index')->with('success', __('Konversi berhasil diperbarui.'));
    }

    /**
     * Hapus data konversi yang dipilih.
     */
    public function destroy(Konversi $konversi): RedirectResponse
    {
        try {
            $konversi->delete();

            return to_route('konversi.index')->with('success', __('Konversi berhasil dihapus.'));
        } catch (\Exception $e) {
            return to_route('konversi.index')->with('error', __('Konversi tidak dapat dihapus karena masih terhubung dengan tabel lain.'));
        }
    }
}
