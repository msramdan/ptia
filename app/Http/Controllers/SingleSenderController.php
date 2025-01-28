<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\{RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;

class SingleSenderController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:single sender view', only: ['index']),
            new Middleware('permission:single sender create', only: ['store']),
        ];
    }

    public function index(): View | RedirectResponse
    {
        $activeSession = DB::table('sessions')
            ->where('is_aktif', 'Yes')
            ->first();
        if ($activeSession) {
            return view('single-sender.create', ['activeSession' => $activeSession]);
        } else {
            return to_route('wa-blast.index')->with('error', __('No active WhatsApp session found.'));
        }
    }

    public function store($request): RedirectResponse
    {

        return to_route('single-sender.index')->with('success', __('The single sender was created successfully.'));
    }
}
