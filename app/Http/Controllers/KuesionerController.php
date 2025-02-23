<?php

namespace App\Http\Controllers;

use App\Models\Kuesioner;
use App\Http\Requests\Kuesioners\{StoreKuesionerRequest, UpdateKuesionerRequest};
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KuesionerController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:kuesioner view', only: ['index', 'show']),
            new Middleware('permission:kuesioner create', only: ['create', 'store']),
            new Middleware('permission:kuesioner edit', only: ['edit', 'update']),
            new Middleware('permission:kuesioner delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $query = DB::table('kuesioner')
                ->join('aspek', 'kuesioner.aspek_id', '=', 'aspek.id')
                ->Join('diklat_type', 'aspek.diklat_type_id', '=', 'diklat_type.id')
                ->select('kuesioner.*', 'aspek.aspek', 'diklat_type.nama_diklat_type');

            if (!empty($request->diklatType)) {
                $query->where('aspek.diklat_type_id', $request->diklatType);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', 'kuesioner.include.action')
                ->rawColumns(['level', 'action'])
                ->toJson();
        }

        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        $selectedDiklatType = $request->diklatType;

        return view('kuesioner.index', compact('diklatTypes', 'selectedDiklatType'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        $aspeks = DB::table('aspek')
            ->join('diklat_type', 'aspek.diklat_type_id', '=', 'diklat_type.id')
            ->select('aspek.id', 'aspek.aspek', 'aspek.diklat_type_id', 'diklat_type.nama_diklat_type')
            ->get();

        return view('kuesioner.create', compact('diklatTypes', 'aspeks'));
    }

    /**
     * Simpan data baru ke dalam penyimpanan.
     */
    public function store(StoreKuesionerRequest $request): RedirectResponse
    {
        Kuesioner::create($request->validated());

        return to_route('kuesioner.index')->with('success', __('Kuesioner berhasil dibuat.'));
    }

    /**
     * Tampilkan data kuesioner yang dipilih.
     */
    public function show(int $id): View
    {
        $kuesioner = DB::table('kuesioner')
            ->join('aspek', 'kuesioner.aspek_id', '=', 'aspek.id')
            ->leftJoin('diklat_type', 'aspek.diklat_type_id', '=', 'diklat_type.id')
            ->select(
                'kuesioner.*',
                'aspek.aspek',
                'diklat_type.nama_diklat_type'
            )
            ->where('kuesioner.id', $id)
            ->first();

        if (!$kuesioner) {
            abort(404);
        }

        return view('kuesioner.show', compact('kuesioner'));
    }

    /**
     * Tampilkan formulir untuk mengedit data kuesioner.
     */
    public function edit($id): View
    {
        $kuesioner = DB::table('kuesioner')
            ->join('aspek', 'kuesioner.aspek_id', '=', 'aspek.id')
            ->join('diklat_type', 'aspek.diklat_type_id', '=', 'diklat_type.id')
            ->select('kuesioner.*', 'aspek.aspek', 'aspek.diklat_type_id', 'diklat_type.nama_diklat_type')
            ->where('kuesioner.id', $id)
            ->first(); // Pakai first() karena kita ambil satu record

        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        $aspeks = DB::table('aspek')
            ->join('diklat_type', 'aspek.diklat_type_id', '=', 'diklat_type.id')
            ->select('aspek.id', 'aspek.aspek', 'aspek.diklat_type_id', 'diklat_type.nama_diklat_type')
            ->get();

        return view('kuesioner.edit', compact('kuesioner', 'diklatTypes', 'aspeks'));
    }

    /**
     * Perbarui data kuesioner yang dipilih.
     */
    public function update(UpdateKuesionerRequest $request, Kuesioner $kuesioner): RedirectResponse
    {
        $kuesioner->update($request->validated());

        return to_route('kuesioner.index')->with('success', __('Kuesioner berhasil diperbarui.'));
    }

    /**
     * Hapus data kuesioner yang dipilih.
     */
    public function destroy(Kuesioner $kuesioner): RedirectResponse
    {
        try {
            $kuesioner->delete();

            return to_route('kuesioner.index')->with('success', __('Kuesioner berhasil dihapus.'));
        } catch (\Exception $e) {
            return to_route('kuesioner.index')->with('error', __("Kuesioner tidak dapat dihapus karena masih terkait dengan tabel lain."));
        }
    }
}
