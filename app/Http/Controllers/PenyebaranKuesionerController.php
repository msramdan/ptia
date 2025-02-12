<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class PenyebaranKuesionerController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:penyebaran kuesioner view', only: ['index']),
        ];
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $projects = DB::table('project')
                ->join('users', 'project.user_id', '=', 'users.id')
                ->leftJoin('project_responden', 'project_responden.project_id', '=', 'project.id')
                ->select(
                    'project.*',
                    'users.name as user_name',
                    'users.email',
                    'users.avatar',
                    // Total responden (semua)
                    DB::raw('COUNT(project_responden.id) as total_responden'),
                    // Total yang sudah mengisi kuesioner alumni
                    DB::raw('SUM(CASE WHEN project_responden.status_pengisian_kuesioner_alumni = "Sudah" THEN 1 ELSE 0 END) as total_sudah_isi'),
                    // Total responden atasan (hanya yang nama_atasan tidak null)
                    DB::raw('SUM(CASE WHEN project_responden.nama_atasan IS NOT NULL THEN 1 ELSE 0 END) as total_responden_atasan'),
                    // Total keterisian atasan (bandingkan dengan semua responden, tidak hanya yang nama_atasan tidak null)
                    DB::raw('SUM(CASE WHEN project_responden.status_pengisian_kuesioner_atasan = "Sudah" THEN 1 ELSE 0 END) as total_sudah_isi_atasan')
                )
                ->where('project.status', 'Pelaksanaan')
                ->groupBy('project.id', 'users.name', 'users.email', 'users.avatar')
                ->orderBy('project.id', 'desc')
                ->get();


            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('responden_alumni', function ($row) {
                    return '
                        <div class="text-center">
                            <a href="#"
                               class="btn btn-sm btn-warning"
                               style="width: 120px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                <i class="fas fa-users"></i> ' . $row->total_responden . ' Alumni
                            </a>
                        </div>';
                })

                ->addColumn('keterisian_alumni', function ($row) {
                    $total = $row->total_responden ?: 1; // Hindari pembagian dengan nol
                    $sudah = $row->total_sudah_isi;
                    $persentase = round(($sudah / $total) * 100, 2);

                    return "$sudah Alumni ($persentase%)";
                })


                ->addColumn('config_alumni', function ($row) {
                    $editBobot = '';
                    return '
                        <div class="d-flex flex-column">
                            <div class="d-flex gap-1 mb-1">
                                <a href="' . $editBobot . '"
                                   class="btn btn-sm btn-success"
                                   data-toggle="tooltip" data-placement="left" title="Pesan WA Alumni">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="' . $editBobot . '"
                                   class="btn btn-sm btn-primary"
                                   data-toggle="tooltip" data-placement="left" title="Kuesioner Alumni">
                                    <i class="fa fa-file"></i>
                                </a>
                            </div>

                            <div class="d-flex gap-1 mb-1">
                                <a href="' . $editBobot . '"
                                    class="btn btn-sm btn-danger"
                                    data-toggle="tooltip" data-placement="left" title="Bobot Alumni">
                                    <i class="fas fa-balance-scale"></i>
                                 </a>
                            </div>
                        </div>';
                })


                ->addColumn('responden_atasan', function ($row) {
                    return '
                        <div class="text-center">
                             <a href="#"
                               class="btn btn-sm btn-warning"
                               style="width: 120px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                 <i class="fas fa-users"></i> ' . $row->total_responden_atasan . ' Atasan
                            </a>
                        </div>';
                })

                ->addColumn('keterisian_atasan', function ($row) {
                    $total = $row->total_responden ?: 1; // Hindari pembagian dengan nol
                    $sudah = $row->total_sudah_isi_atasan;
                    $persentase = round(($sudah / $total) * 100, 2);

                    return "$sudah Atasan ($persentase%)";
                })

                ->addColumn('config_atasan', function ($row) {
                    $editBobot = '';
                    return '
                        <div class="text-center d-flex flex-column align-items-center">
                            <div class="d-flex gap-1 mb-1">
                                <a href="' . $editBobot . '"
                                   class="btn btn-sm btn-success"
                                   data-toggle="tooltip" data-placement="left" title="Pesan WA Alumni">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="' . $editBobot . '"
                                   class="btn btn-sm btn-primary"
                                   data-toggle="tooltip" data-placement="left" title="Kuesioner Alumni">
                                    <i class="fa fa-file"></i>
                                </a>
                            </div>
                        </div>';
                })


                ->addColumn('user', function ($row) {
                    $avatar = $row->avatar
                        ? asset("storage/uploads/avatars/$row->avatar")
                        : "https://www.gravatar.com/avatar/" . md5(strtolower(trim($row->email))) . "&s=450";

                    return '
                        <div class="d-flex align-items-center">
                            <img src="' . e($avatar) . '" class="img-thumbnail"
                                 style="width: 50px; height: 50px; border-radius: 5%; margin-right: 10px;">
                            <span>' . e($row->user_name) . '</span>
                        </div>';
                })
                ->addColumn('action', 'penyebaran-kuesioner.include.action')
                ->rawColumns(['action', 'responden_alumni', 'responden_atasan', 'keterisian_alumni', 'keterisian_atasan', 'config_alumni', 'config_atasan', 'user'])
                ->toJson();
        }

        return view('penyebaran-kuesioner.index');
    }
}
