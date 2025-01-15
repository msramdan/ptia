<?php

namespace App\Http\Controllers;

use App\Models\BobotAspek;
use App\Http\Requests\BobotAspeks\{UpdateBobotAspekRequest};
use Illuminate\Contracts\View\View;
use Illuminate\Http\{RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class BobotAspekController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:bobot aspek view', only: ['index']),
            new Middleware('permission:bobot aspek edit', only: ['update']),
        ];
    }

    public function index(): View
    {
        $bobotAspek = BobotAspek::findOrFail(1)->first();
        return view('bobot-aspek.edit', compact('bobotAspek'));
    }

    public function update(UpdateBobotAspekRequest $request, BobotAspek $bobotAspek): RedirectResponse
    {

        $bobotAspek->update($request->validated());

        return to_route('bobot-aspek.index')->with('success', __('The bobot aspek was updated successfully.'));
    }
}
