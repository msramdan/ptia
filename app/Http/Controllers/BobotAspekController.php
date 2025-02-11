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
        $diklatTypeId = request()->query('diklatType');
        if (!$diklatTypeId) {
            $firstDiklatType = DB::table('diklat_type')
                ->select('id', 'nama_diklat_type')
                ->first();
            $diklatTypeId = $firstDiklatType->id;
        }

        // Fetch bobotAspek based on the diklatType
        $bobotAspek = DB::table('bobot_aspek')
            ->leftJoin('aspek', 'bobot_aspek.aspek_id', '=', 'aspek.id')
            ->select('bobot_aspek.*', 'aspek.aspek as aspek_nama', 'aspek.level')
            ->where('aspek.diklat_type_id', $diklatTypeId)
            ->get();

        // Grouping by level 3 and 4
        $level3 = $bobotAspek->where('level', 3);
        $level4 = $bobotAspek->where('level', 4);

        // Fetch secondary data
        $dataSecondary = DB::table('bobot_aspek_sekunder')
            ->select('bobot_aspek_sekunder.*')
            ->where('diklat_type_id', $diklatTypeId)
            ->first();

        // Fetch all diklatTypes for the dropdown
        $diklatTypes = DB::table('diklat_type')->select('id', 'nama_diklat_type')->get();

        return view('bobot-aspek.edit', compact('level3', 'level4', 'dataSecondary', 'diklatTypes'));
    }



    public function update(Request $request): RedirectResponse
    {

        DB::transaction(function () use ($request) {
            // Update Level 3
            if ($request->has('level3')) {
                foreach ($request->level3 as $data) {
                    if (!empty($data['id'])) {
                        DB::table('bobot_aspek')->where('id', $data['id'])->update([
                            'bobot_alumni' => $data['bobot_alumni'] ?? 0,
                            'bobot_atasan_langsung' => $data['bobot_atasan_langsung'] ?? 0,
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // Update Level 4
            if ($request->has('level4')) {
                foreach ($request->level4 as $data) {
                    if (!empty($data['id'])) {
                        DB::table('bobot_aspek')->where('id', $data['id'])->update([
                            'bobot_alumni' => $data['bobot_alumni'] ?? 0,
                            'bobot_atasan_langsung' => $data['bobot_atasan_langsung'] ?? 0,
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // Update bobot_aspek_sekunder
            if ($request->has('bobot_aspek_sekunder_id') && !empty($request->bobot_aspek_sekunder_id)) {
                DB::table('bobot_aspek_sekunder')->where('id', $request->bobot_aspek_sekunder_id)->update([
                    'bobot_aspek_sekunder' => $request->bobot_aspek_sekunder ?? 0,
                    'updated_at' => now()
                ]);
            }
        });

        return redirect()->back()->with('success', 'Bobot aspek berhasil diperbarui.');
    }
}
