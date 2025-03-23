<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;

class AutoInsertKuesionerAtasanController extends Controller
{

    public function insertData()
    {
        // Ambil data dari project_responden yang deadline_pengisian_atasan lebih dari hari ini
        $respondenList = DB::table('project_responden')
            ->whereDate('deadline_pengisian_atasan', '<', Carbon::now()->toDateString()) // Cek yang sudah expired
            ->where('status_pengisian_kuesioner_atasan', 'Belum') // Status masih "Belum"
            ->limit(10)
            ->get();

        // Looping setiap responden
        foreach ($respondenList as $responden) {
            // Ambil data dari project_kuesioner yang terkait dengan project_id dan remark = 'Atasan'
            $kuesionerList = DB::table('project_kuesioner')
                ->where('project_kuesioner.project_id', $responden->project_id)
                ->where('project_kuesioner.remark', 'Atasan')
                ->select('project_kuesioner.*')
                ->get();
            // Proses looping data project_kuesioner dan project_jawaban_kuesioner
            foreach ($kuesionerList as $kuesioner) {
                // DB::table('some_table')->insert([
                //     'project_responden_id' => $responden->id,
                //     'project_kuesioner_id' => $kuesioner->id,
                //     'jawaban' => $kuesioner->jawaban ?? null,
                //     'created_at' => now(),
                //     'updated_at' => now(),
                // ]);
            }
        }

        return response()->json(['message' => 'Data inserted successfully']);
    }
}
