<?php

namespace App\Http\Controllers;

use App\Models\BobotAspek;
use Illuminate\Contracts\View\View;
use Illuminate\Http\{RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        $bobotAspek = DB::table('bobot_aspek')
            ->join('aspek', 'bobot_aspek.aspek_id', '=', 'aspek.id')
            ->select('bobot_aspek.*', 'aspek.aspek as aspek_nama', 'aspek.level')
            ->get();

        // Grouping by level 3 and 4
        $level3 = $bobotAspek->where('level', 3);
        $level4 = $bobotAspek->where('level', 4);

        return view('bobot-aspek.edit', compact('level3', 'level4'));
    }



    public function update(Request $request, BobotAspek $bobotAspek): RedirectResponse
    {

        $bobotAspek->update($request->validated());

        return to_route('bobot-aspek.index')->with('success', __('The bobot aspek was updated successfully.'));
    }
}
