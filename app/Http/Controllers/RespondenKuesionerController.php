<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RespondenKuesionerController extends Controller
{
    public function index($encryptedId, $encryptedTarget)
    {
        $id = decryptShort($encryptedId);
        $target = decryptShort($encryptedTarget);
        try {

            if (!in_array($target, ['alumni', 'atasan'])) {
                abort(404);
            }

            $responden = DB::table('project_responden')->where('id', $id)->first();

            if (!$responden) {
                abort(404);
            }

            return view('kuesioner', compact('responden', 'target'));
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
