<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public function store(Request $request): RedirectResponse
    {
        $baseUrl = env('BASE_NODE', 'http://192.168.10.36:3301');
        $payload = [
            'api_key' =>  $request->api_key,
            'receiver' => $request->no_wa_tujuan,
            'data' => [
                'message' => $request->isi_pesan,
            ],
        ];

        try {
            $response = Http::post("{$baseUrl}/api/send-message", $payload);

            if ($response->successful()) {
                return to_route('single-sender.index')->with('success', __('The single sender was sent successfully.'));
            } else {
                return to_route('single-sender.index')->with('error', __('The message could not be sent.'));
            }
        } catch (\Exception $e) {
            return to_route('single-sender.index')->with('error', __('An error occurred while sending the message.'));
        }
    }
}
