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
            $projects = Project::query();

            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('action', 'project.include.action')
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

        // Cek apakah kaldikID sudah ada di dalam tabel
        $existingProject = DB::table('project')->where('kaldikID', $data['kaldikID'])->first();

        if ($existingProject) {
            return response()->json([
                'status'  => false,
                'message' => "Project with Kaldik ID {$data['kaldikID']} already exists in project management.",
            ], 409);
        }

        $kode_project = Str::upper(Str::random(8));

        $insertData = [
            'kode_project' => $kode_project,
            'kaldikID'     => $data['kaldikID'],
            'kaldikDesc'   => $data['kaldikDesc'],
            'created_at'   => now(),
            'updated_at'   => now(),
        ];

        DB::table('project')->insert($insertData);

        return response()->json([
            'status'  => true,
            'message' => 'Project created successfully',
            'data'    => $insertData,
        ]);
    }


    public function destroy(Project $project): RedirectResponse
    {
        try {
            $project->delete();

            return to_route('project.index')->with('success', __('The project was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('project.index')->with('error', __("The project can't be deleted because it's related to another table."));
        }
    }
}
