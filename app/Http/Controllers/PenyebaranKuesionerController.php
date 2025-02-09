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
                ->addColumn('kuesioner', function ($row) {
                    $editAlumniUrl = route('project.kuesioner.show', ['id' => $row->id, 'remark' => 'Alumni']);
                    $editAtasanUrl = route('project.kuesioner.show', ['id' => $row->id, 'remark' => 'Atasan']);
                    return '
                        <div class="text-center">
                            <a href="' . $editAlumniUrl . '"
                               class="btn btn-sm btn-primary"
                               style="width: 100px; background: #007bff; border-color: #007bff;"
                               data-toggle="tooltip" data-placement="left" title="Alumni: Ubah Kuesioner">
                                <i class="fas fa-clipboard-list"></i> Alumni
                            </a>
                            <br><hr style="margin: 5px;">
                            <a href="' . $editAtasanUrl . '"
                               class="btn btn-sm btn-secondary"
                               style="width: 100px; background: #6c757d; border-color: #6c757d;"
                               data-toggle="tooltip" data-placement="left" title="Atasan Langsung: Ubah Kuesioner">
                                <i class="fas fa-clipboard-list"></i> Atasan
                            </a>
                        </div>';
                })

                ->addColumn('responden', function ($row) {
                    $editResponden = route('project.responden.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                            <a href="' . $editResponden . '" class="btn btn-sm btn-warning"
                               style="width: 140px; background: #ffc107; border-color: #ffc107;"
                               data-toggle="tooltip" data-placement="left" title="Atur Responden">
                                <i class="fas fa-users-cog"></i> Atur Responden
                            </a>
                        </div>';
                })
                ->addColumn('bobot', function ($row) {
                    $editBobot = route('project.bobot.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                             <a href="' . $editBobot . '"
                               class="btn btn-sm btn-danger"
                               style="width: 140px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                <i class="fas fa-balance-scale"></i> Atur Botot
                            </a>
                        </div>';
                })

                ->addColumn('wa', function ($row) {
                    $editWa = route('project.pesan.wa.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                            <a href="' . $editWa . '"
                               class="btn btn-sm btn-success"
                               style="width: 140px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Pesan">
                                <i class="fab fa-whatsapp"></i> Atur Pesan
                            </a>
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
                ->addColumn('action', 'project.include.action')
                ->rawColumns(['kuesioner', 'responden', 'bobot', 'user', 'wa', 'action'])
                ->toJson();
        }

        return view('project.index');
    }
}
