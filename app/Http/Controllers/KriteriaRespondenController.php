<?php

namespace App\Http\Controllers;

use App\Models\KriteriaResponden;
use App\Http\Requests\KriteriaRespondens\{UpdateKriteriaRespondenRequest};
use Illuminate\Contracts\View\View;
use Illuminate\Http\{RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;

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
        $kriteriaResponden = KriteriaResponden::findOrFail(1);
        $kriteriaResponden->nilai_post_test = json_decode($kriteriaResponden->nilai_post_test, true);
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        return view('kriteria-responden.edit', compact('kriteriaResponden','diklatTypes'));
    }

    public function update(UpdateKriteriaRespondenRequest $request, KriteriaResponden $kriteriaResponden): RedirectResponse
    {
        $data = $request->validated();
        $data['nilai_post_test'] = $data['nilai_post_test'] ?? [];

        $kriteriaResponden->update($data);

        return to_route('kriteria-responden.index')->with('success', __('The kriteria responden was updated successfully.'));
    }
}
