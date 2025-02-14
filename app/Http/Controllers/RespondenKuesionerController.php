<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RespondenKuesionerController extends Controller
{
    public function index($encryptedId, $encryptedTarget)
    {
        try {
            $id = decryptShort($encryptedId);
            $target = decryptShort($encryptedTarget);
            if (!in_array($target, ['alumni', 'atasan'])) {
                return response()->view('errors.404', [], 404);
            }
            $responden = DB::table('project_responden')->where('id', $id)->first();

            if (!$responden) {
                return response()->view('errors.404', [], 404);
            }

            return view('kuesioner', compact('responden', 'target'));
        } catch (\Exception $e) {
            return response()->view('errors.404', [], 404);
        }
    }


    public function generateUrl($id, $target)
    {
        // Pastikan target valid sebelum dienkripsi
        if (!in_array($target, ['alumni', 'atasan'])) {
            return response()->json(['error' => 'Target tidak valid'], 400);
        }

        $encryptedId = encryptShort($id);
        $encryptedTarget = encryptShort($target);

        $url = url("/responden-kuesioner/{$encryptedId}/{$encryptedTarget}");

        return response()->json(['url' => $url]);
    }
}
