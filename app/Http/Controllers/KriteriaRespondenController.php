<?php

namespace App\Http\Controllers;

use App\Models\KriteriaResponden;
use App\Http\Requests\KriteriaRespondens\{UpdateKriteriaRespondenRequest};
use Illuminate\Contracts\View\View;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

    public function index(Request $request): View|JsonResponse
    {

        $diklatTypeId = request()->query('diklatType');
        if (!$diklatTypeId) {
            $firstDiklatType = DB::table('diklat_type')
                ->select('id', 'nama_diklat_type')
                ->first();
            $diklatTypeId = $firstDiklatType->id;
        }

        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();
        $kriteriaResponden = KriteriaResponden::where('diklat_type_id', $diklatTypeId)->first();

        if ($kriteriaResponden) {
            $kriteriaResponden->nilai_post_test = json_decode($kriteriaResponden->nilai_post_test, true);
        }

        return view('kriteria-responden.edit', compact('kriteriaResponden', 'diklatTypes', 'diklatTypeId'));
    }

    public function update(UpdateKriteriaRespondenRequest $request, KriteriaResponden $kriteriaResponden): RedirectResponse
    {
        $data = $request->validated();
        $data['nilai_post_test'] = $data['nilai_post_test'] ?? [];

        $kriteriaResponden->update($data);

        return back()->with('success', __('The kriteria responden was updated successfully.'));
    }

}
