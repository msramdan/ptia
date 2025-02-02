<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

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
                ->addColumn('diklat', function ($row) {
                    return $row->kaldikID .' - '. $row->kaldikDesc;
                })
                ->addColumn('action', 'project.include.action')
                ->toJson();
        }

        return view('project.index');
    }

    public function store(Request $request): RedirectResponse
    {

        Project::create($request->validated());

        return to_route('project.index')->with('success', __('The project was created successfully.'));
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
