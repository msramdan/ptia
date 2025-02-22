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

            if (!in_array($target, ['Alumni', 'Atasan'])) {
                abort(404);
            }

            $responden = DB::table('project_responden')
                ->join('project', 'project_responden.project_id', '=', 'project.id')
                ->select(
                    'project_responden.*',
                    'project.status',
                    'project.kaldikID',
                    'project.kaldikDesc',
                    'project_responden.deadline_pengisian_alumni',
                    'project_responden.deadline_pengisian_atasan'
                )
                ->where('project_responden.id', $id)
                ->first();

            if (!$responden) {
                abort(404);
            }

            $kuesioner = DB::table('project_kuesioner')
                ->where('remark', $target)
                ->where('project_id', $responden->project_id)
                ->get();

            // Tentukan deadline berdasarkan target
            $deadline = $target === 'Alumni' ? $responden->deadline_pengisian_alumni : $responden->deadline_pengisian_atasan;

            // Cek apakah sudah lewat deadline
            $isExpired = $deadline && now()->gt($deadline);

            return view('kuesioner', compact('responden', 'target', 'kuesioner', 'isExpired'));
        } catch (\Exception $e) {
            abort(404);
        }
    }




    public function generateUrl($id, $target)
    {
        if (!in_array($target, ['alumni', 'atasan'])) {
            return response()->json(['error' => 'Target tidak valid'], 400);
        }

        $encryptedId = encryptShort($id);
        $encryptedTarget = encryptShort($target);

        $url = url("/responden-kuesioner/{$encryptedId}/{$encryptedTarget}");

        return response()->json(['url' => $url]);
    }
}
