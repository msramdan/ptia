<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class LogActivityController extends Controller
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:log activity view', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Eager load relasi 'causer' untuk efisiensi
            $query = Activity::with('causer')->latest();

            // Biarkan DataTables yang menangani data mentah
            return DataTables::of($query)
                ->editColumn('created_at', function ($activity) {
                    return $activity->created_at->format('d M Y, H:i:s');
                })
                ->make(true);
        }

        return view('log-activities.index');
    }
}
