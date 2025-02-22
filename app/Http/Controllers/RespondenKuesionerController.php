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
                    'project_responden.deadline_pengisian_atasan',
                    'project_responden.status_pengisian_kuesioner_alumni', // Tambahkan status alumni
                    'project_responden.status_pengisian_kuesioner_atasan'  // Tambahkan status atasan
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
        ]);

        try {
            DB::beginTransaction(); // Mulai transaksi

            $dataToInsert = [];
            foreach ($validatedData['project_kuesioner_id'] as $kuesionerId => $projectKuesionerId) {
                $nilaiSebelum = $validatedData['sebelum'][$kuesionerId] ?? 0;
                $nilaiSesudah = $validatedData['sesudah'][$kuesionerId] ?? 0;
                $catatan = $validatedData['catatan'][$kuesionerId] ?? null;

                $dataToInsert[] = [
                    'project_kuesioner_id' => $projectKuesionerId,
                    'project_responden_id' => $validatedData['project_responden_id'],
                    'nilai_sebelum' => $nilaiSebelum,
                    'nilai_sesudah' => $nilaiSesudah,
                    'catatan' => $catatan,
                    'remark' => $validatedData['remark'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert data ke dalam tabel project_jawaban_kuesioner
            DB::table('project_jawaban_kuesioner')->insert($dataToInsert);

            // Menentukan field yang diupdate berdasarkan remark
            $fieldToUpdate = ($validatedData['remark'] === 'Alumni') ?
                'status_pengisian_kuesioner_alumni' : 'status_pengisian_kuesioner_atasan';

            // Update status di project_responden
            DB::table('project_responden')
                ->where('id', $validatedData['project_responden_id'])
                ->update([$fieldToUpdate => 'Sudah']);

            DB::commit();

            return redirect()->back()->with('success', 'Jawaban berhasil disimpan dan status diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
