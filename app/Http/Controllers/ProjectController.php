<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:project view', only: ['index', 'show']),
            new Middleware('permission:generate project', only: ['store']),
            new Middleware('permission:project print', only: ['print']),
            new Middleware('permission:project delete', only: ['destroy']),
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
            ->select('project.*', 'users.name as user_name', 'users.email', 'users.avatar');

            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('kuesioner', function ($row) {
                    return '
                        <div class="text-center">
                            <a href="javascript:void(0);" onclick="quis_edit(' . $row->id . ', 1)"
                               class="btn btn-sm btn-primary"
                               style="width: 100px; background: #007bff; border-color: #007bff;"
                               data-toggle="tooltip" data-placement="left" title="Alumni: Ubah Kuesioner">
                                <i class="fas fa-clipboard-list"></i> Alumni
                            </a>
                            <br><hr style="margin: 5px;">
                            <a href="javascript:void(0);" onclick="quis_edit(' . $row->id . ', 2)"
                               class="btn btn-sm btn-secondary"
                               style="width: 100px; background: #6c757d; border-color: #6c757d;"
                               data-toggle="tooltip" data-placement="left" title="Atasan Langsung: Ubah Kuesioner">
                                <i class="fas fa-clipboard-list"></i> Atasan
                            </a>
                        </div>';
                })
                ->addColumn('peserta', function ($row) {
                    return '
                        <div class="text-center">
                            <a href="javascript:void(0);" onclick="peserta_view(' . $row->id . ')"
                               class="btn btn-sm btn-info"
                               style="width: 160px; background: #17a2b8; border-color: #17a2b8;"
                               data-toggle="tooltip" data-placement="left" title="Lihat Daftar Responden">
                                <i class="fas fa-eye"></i> Daftar Responden
                            </a>
                            <br><hr style="margin: 5px;">
                            <a href="javascript:void(0);" onclick="peserta_edit(' . $row->id . ')"
                               class="btn btn-sm btn-warning"
                               style="width: 160px; background: #ffc107; border-color: #ffc107;"
                               data-toggle="tooltip" data-placement="left" title="Atur Responden">
                                <i class="fas fa-users-cog"></i> Atur Responden
                            </a>
                        </div>';
                })
                ->addColumn('bobot', function ($row) {
                    return '
                        <div class="text-center">
                            <a href="javascript:void(0);" onclick="bobot_edit(' . $row->id . ')"
                               class="btn btn-sm btn-danger"
                               style="width: 120px; background: #dc3545; border-color: #dc3545;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                <i class="fas fa-balance-scale"></i> Atur Bobot
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
                ->rawColumns(['kuesioner', 'peserta', 'bobot', 'user', 'action'])
                ->toJson();
        }

        return view('project.index');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'kaldikID'   => 'required|numeric',
            'kaldikDesc' => 'required|string',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Cek apakah kaldikID sudah ada di dalam tabel project
            $existingProject = DB::table('project')->where('kaldikID', $data['kaldikID'])->first();

            if ($existingProject) {
                return response()->json([
                    'status'  => false,
                    'message' => "Project with Kaldik ID {$data['kaldikID']} already exists in project management.",
                ], 409);
            }

            $kode_project = Str::upper(Str::random(8));
            $userId = auth()->id();

            // Insert ke tabel project
            $projectId = DB::table('project')->insertGetId([
                'kode_project'  => $kode_project,
                'kaldikID'      => $data['kaldikID'],
                'kaldikDesc'    => $data['kaldikDesc'],
                'user_id'       => $userId, // Simpan ID user yang login
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Ambil data pertama dari tabel kriteria_responden
            $kriteriaResponden = DB::table('kriteria_responden')->first();

            if (!$kriteriaResponden) {
                throw new \Exception("No kriteria responden data found.");
            }

            // Insert ke tabel project_kriteria_responden
            DB::table('project_kriteria_responden')->insert([
                'project_id'              => $projectId,
                'nilai_post_test'         => $kriteriaResponden->nilai_post_test,
                'nilai_pre_test_minimal'  => $kriteriaResponden->nilai_pre_test_minimal,
                'nilai_post_test_minimal' => $kriteriaResponden->nilai_post_test_minimal,
                'nilai_kenaikan_pre_post' => $kriteriaResponden->nilai_kenaikan_pre_post,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            // Commit transaksi jika semua proses sukses
            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Project created successfully',
                'data'    => [
                    'kode_project' => $kode_project,
                    'kaldikID'     => $data['kaldikID'],
                    'kaldikDesc'   => $data['kaldikDesc'],
                ],
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Failed to create project: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id): RedirectResponse
    {
        try {
            $deleted = DB::table('project')->where('id', $id)->delete();

            if ($deleted) {
                return to_route('project.index')->with('success', __('The project was deleted successfully.'));
            } else {
                return to_route('project.index')->with('error', __("The project was not found or couldn't be deleted."));
            }
        } catch (\Exception $e) {
            return to_route('project.index')->with('error', __("The project can't be deleted because it's related to another table."));
        }
    }
}
