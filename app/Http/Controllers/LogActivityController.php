<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Routing\Controllers\Middleware;

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

    public function index()
    {
        $activities = Activity::latest()->paginate(15);

        return view('log-activities.index', compact('activities'));
    }
}
