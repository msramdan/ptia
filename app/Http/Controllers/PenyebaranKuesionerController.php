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
                ->select('project.*', 'users.name as user_name', 'users.email', 'users.avatar')
                ->orderBy('project.id', 'desc') // Urutkan berdasarkan ID secara descending
                ->get();
            return DataTables::of($projects)
                ->addIndexColumn()

                ->addColumn('responden_alumni', function ($row) {
                    $editBobot = route('project.bobot.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                             <a href="' . $editBobot . '"
                               class="btn btn-sm btn-warning"
                               style="width: 120px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                 <i class="fas fa-users"></i> 999 Alumni
                            </a>
                        </div>';
                })

                ->addColumn('keterisian_alumni', function ($row) {
                    return '10 Alumni (100%)';
                })

                ->addColumn('config_alumni', function ($row) {
                    $editBobot = route('project.bobot.show', ['id' => $row->id]);
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
                    $editBobot = route('project.bobot.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                             <a href="' . $editBobot . '"
                               class="btn btn-sm btn-warning"
                               style="width: 120px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                 <i class="fas fa-users"></i> 999 Atasan
                            </a>
                        </div>';
                })

                ->addColumn('keterisian_atasan', function ($row) {
                    return '10 Atasan (50%)';
                })

                ->addColumn('config_atasan', function ($row) {
                    $editBobot = route('project.bobot.show', ['id' => $row->id]);
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
