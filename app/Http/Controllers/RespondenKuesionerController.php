<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RespondenKuesionerController extends Controller
{
    public function index($encryptedId, $encryptedTarget, Request $request)
    {
        try {
            $id = decryptShort($encryptedId);
            $target = decryptShort($encryptedTarget);
            $token = $request->query('token'); // Ambil token dari URL

            if (!in_array($target, ['Alumni', 'Atasan'])) {
                abort(404);
            }

            // Ambil responden dengan data project dan token
            $responden = DB::table('project_responden')
                ->join('project', 'project_responden.project_id', '=', 'project.id')
                ->select(
                    'project_responden.*',
                    'project.status',
                    'project.kaldikID',
                    'project.kaldikDesc',
                    'project_responden.deadline_pengisian_alumni',
                    'project_responden.deadline_pengisian_atasan',
                    'project_responden.status_pengisian_kuesioner_alumni',
                    'project_responden.status_pengisian_kuesioner_atasan'
                )
                ->where('project_responden.id', $id)
                ->first();

            if (!$responden) {
                abort(404);
            }

            // Cek apakah token valid
            if (!$token || $token !== $responden->token) {
                abort(403, 'Token tidak valid');
            }

            // Ambil semua kuesioner beserta jawabannya dalam satu query
            $kuesioner = DB::table('project_kuesioner as pk')
                ->leftJoin('project_jawaban_kuesioner as pj', function ($join) use ($id, $target) {
                    $join->on('pk.id', '=', 'pj.project_kuesioner_id')
                        ->where('pj.project_responden_id', $id)
                        ->where('pj.remark', $target);
                })
                ->where('pk.remark', $target)
                ->where('pk.project_id', $responden->project_id)
                ->select(
                    'pk.id',
                    'pk.aspek',
                    'pk.kriteria',
                    'pk.pertanyaan',
                    'pj.nilai_sebelum',
                    'pj.nilai_sesudah',
                    'pj.catatan'
                )
                ->get();

            // Tentukan deadline berdasarkan target
            $deadline = $target === 'Alumni' ? $responden->deadline_pengisian_alumni : $responden->deadline_pengisian_atasan;

            // Cek apakah sudah lewat deadline
            $isExpired = $deadline && now()->gt($deadline);

            // Cek apakah kuesioner sudah diisi
            $statusPengisian = ($target === 'Alumni') ?
                $responden->status_pengisian_kuesioner_alumni :
                $responden->status_pengisian_kuesioner_atasan;

            $sudahMengisi = ($statusPengisian === 'Sudah');

            return view('kuesioner', compact('responden', 'target', 'kuesioner', 'isExpired', 'sudahMengisi'));
        } catch (\Exception $e) {
            abort(404);
        }
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'project_kuesioner_id' => 'required|array',
            'project_responden_id' => 'required',
            'sebelum' => 'required|array',
            'sesudah' => 'required|array',
            'catatan' => 'nullable|array',
            'remark' => 'required|in:Alumni,Atasan',
            'atasan' => 'required|string',
            'no_wa' => 'required|string',
        ]);

        try {
            DB::beginTransaction(); // Mulai transaksi

            $dataToInsert = [];
            foreach ($validatedData['project_kuesioner_id'] as $kuesionerId => $projectKuesionerId) {
                $dataToInsert[] = [
                    'project_kuesioner_id' => $projectKuesionerId,
                    'project_responden_id' => $validatedData['project_responden_id'],
                    'nilai_sebelum' => $validatedData['sebelum'][$kuesionerId] ?? 0,
                    'nilai_sesudah' => $validatedData['sesudah'][$kuesionerId] ?? 0,
                    'catatan' => $validatedData['catatan'][$kuesionerId] ?? null,
                    'remark' => $validatedData['remark'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert batch data ke dalam tabel project_jawaban_kuesioner
            DB::table('project_jawaban_kuesioner')->insert($dataToInsert);

            // Menentukan field yang diupdate berdasarkan remark
            $updateData = [
                ($validatedData['remark'] === 'Alumni' ? 'status_pengisian_kuesioner_alumni' : 'status_pengisian_kuesioner_atasan') => 'Sudah'
            ];

            // Jika remark adalah Alumni, tambahkan update nama_atasan, telepon_atasan, dan deadline_pengisian_atasan
            if ($validatedData['remark'] === 'Alumni') {
                $updateData['nama_atasan'] = $validatedData['atasan'] ?? null;
                $updateData['telepon_atasan'] = $validatedData['no_wa'] ?? null;
                $updateData['deadline_pengisian_atasan'] = now()->addDays((int) env('DEADLINE_PENGISIAN', 7))->format('Y-m-d');
            }

            // Update status di project_responden
            DB::table('project_responden')
                ->where('id', $validatedData['project_responden_id'])
                ->update($updateData);

            DB::commit();

            return redirect()->back()->with('success', 'Jawaban berhasil disimpan dan status diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
