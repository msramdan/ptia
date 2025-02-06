<?php

namespace App\Http\Controllers;

use App\Models\PesanWa;
use App\Http\Requests\PesanWas\{UpdatePesanWaRequest};
use Illuminate\Contracts\View\View;
use Illuminate\Http\{RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class PesanWaController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:pesan wa view', only: ['index']),
            new Middleware('permission:pesan wa edit', only: ['update']),
        ];
    }

    public function index(): View
    {
        $pesanWa = PesanWa::findOrFail(1)->first();
        return view('pesan-wa.edit', compact('pesanWa'));
    }

    public function update(UpdatePesanWaRequest $request, PesanWa $pesanWa): RedirectResponse
    {

        $pesanWa->update($request->validated());

        return to_route('pesan-wa.index')->with('success', __('The pesan wa was updated successfully.'));
    }
}
