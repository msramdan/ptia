<?php

namespace App\Http\Controllers;

use App\Models\KriteriaResponden;
use App\Http\Requests\KriteriaRespondens\{UpdateKriteriaRespondenRequest};
use Illuminate\Contracts\View\View;
use Illuminate\Http\{RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class KriteriaRespondenController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kriteria responden view', only: ['index']),
            new Middleware('permission:kriteria responden edit', only: ['update']),
        ];
    }

    public function index(): View
    {
        $kriteriaResponden = KriteriaResponden::findOrFail(1)->first();
        return view('kriteria-responden.edit', compact('kriteriaResponden'));
    }


    public function update(UpdateKriteriaRespondenRequest $request, KriteriaResponden $kriteriaResponden): RedirectResponse
    {

        $kriteriaResponden->update($request->validated());

        return to_route('kriteria-responden.index')->with('success', __('The kriteria responden was updated successfully.'));
    }
}
