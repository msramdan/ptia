<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class HasilEvaluasiController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:hasil evaluasi view', only: ['index']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $projects = DB::table('project')
                ->join('users', 'project.user_id', '=', 'users.id')
                ->join('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
                ->leftJoin('project_data_sekunder', 'project_data_sekunder.project_id', '=', 'project.id')
                ->select(
                    'project.id',
                    'project.kaldikID',
                    'project.kaldikDesc',
                    'users.name as user_name',
                    'users.email',
                    'users.avatar',
                    'diklat_type.nama_diklat_type',
                    'project_data_sekunder.nilai_kinerja_awal',
                    'project_data_sekunder.nilai_kinerja_akhir',
                    'project_data_sekunder.berkas'
                )
                ->where('project.status', 'Pelaksanaan')
                ->orderBy('project.id', 'desc');

            // Debug query untuk memastikan data ada
            // Hapus baris ini setelah memastikan data ada
            // dd($projects->get());

            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('nama_project', function ($row) {
                    return $row->kaldikDesc;
                })
                ->addColumn('kode_project', function ($row) {
                    return $row->kaldikID;
                })
                ->addColumn('skor_level_3', function ($row) {
                    return $row->nilai_kinerja_awal ?? '-';
                })
                ->addColumn('predikat_level_3', function ($row) {
                    if ($row->nilai_kinerja_awal !== null) {
                        return $this->getPredikat($row->nilai_kinerja_awal);
                    }
                    return '-';
                })
                ->addColumn('skor_level_4', function ($row) {
                    return $row->nilai_kinerja_akhir ?? '-';
                })
                ->addColumn('predikat_level_4', function ($row) {
                    if ($row->nilai_kinerja_akhir !== null) {
                        return $this->getPredikat($row->nilai_kinerja_akhir);
                    }
                    return '-';
                })
                ->rawColumns(['skor_level_3', 'predikat_level_3', 'skor_level_4', 'predikat_level_4'])
                ->toJson();
        }

        return view('hasil-evaluasi.index');
    }

    /**
     * Menentukan predikat berdasarkan skor.
     */
    private function getPredikat($skor): string
    {
        if ($skor >= 80) {
            return 'Sangat Berdampak';
        } elseif ($skor >= 60) {
            return 'Berdampak';
        } elseif ($skor >= 40) {
            return 'Cukup Berdampak';
        } else {
            return 'Kurang Berdampak';
        }
    }
}
