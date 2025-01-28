<?php

namespace App\Http\Controllers;

use App\Models\WaBlast;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Http\Request;

class WaBlastController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:wa blast view', only: ['index', 'show']),
            new Middleware('permission:wa blast create', only: ['create', 'store']),
            new Middleware('permission:wa blast edit', only: ['edit', 'update']),
            new Middleware('permission:wa blast delete', only: ['destroy']),
        ];
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $waBlasts = WaBlast::query();
            return DataTables::of($waBlasts)
                ->addColumn('status', function ($row) {
                    if ($row->status == 'STOPPED') {
                        return '<span class="badge bg-danger">' . $row->status . '</span>';
                    } else {
                        return '<span class="badge bg-success">' . $row->status . '</span>';
                    }
                })
                ->addColumn('action', 'wa-blast.include.action')
                ->rawColumns(['status', 'action'])
                ->toJson();
        }

        return view('wa-blast.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_name' => 'required|string|max:255',
        ]);

        $apiKey = bin2hex(random_bytes(16));

        WaBlast::create([
            'session_name' => $validated['session_name'],
            'status' => 'STOPPED',
            'api_key' => $apiKey,
        ]);
        return to_route('wa-blast.index')->with('success', __('The wa blast was created successfully.'));
    }

    public function show(WaBlast $waBlast): View
    {
        return view('wa-blast.show', compact('waBlast'));
    }

    public function destroy(WaBlast $waBlast): RedirectResponse
    {
        try {
            $waBlast->delete();

            return to_route('wa-blast.index')->with('success', __('The wa blast was deleted successfully.'));
        } catch (\Exception $e) {
            return to_route('wa-blast.index')->with('error', __("The wa blast can't be deleted because it's related to another table."));
        }
    }
}
