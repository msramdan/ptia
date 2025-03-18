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
                    'project.id as project_id',
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
                    'pk.aspek_id',
                    'pk.kriteria',
                    'pk.level',
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
            'project_id' => 'required',
            'sebelum' => 'required|array',
            'sesudah' => 'required|array',
            'catatan' => 'nullable|array',
            'remark' => 'required|in:Alumni,Atasan',
            'atasan' => 'required|string',
            'no_wa' => 'required|string',
            'aspek_id' => 'required|array',
            'level' => 'required|array',
            'kriteria' => 'required|array',
        ]);

        try {

            DB::beginTransaction();
            $diklatTypeId = DB::table('project')
                ->where('id', $validatedData['project_id'])
                ->value('diklat_type_id');

            $dataToInsert = [];

            // Mengisi array data
            foreach ($validatedData['project_kuesioner_id'] as $kuesionerId => $projectKuesionerId) {
                $nilai_sebelum = $validatedData['sebelum'][$kuesionerId] ?? 0;
                $nilai_sesudah = $validatedData['sesudah'][$kuesionerId] ?? 0;
                $nilai_delta = $nilai_sesudah - $nilai_sebelum;

                $dataToInsert[] = [
                    'project_kuesioner_id' => $projectKuesionerId,
                    'project_responden_id' => $validatedData['project_responden_id'],
                    'kriteria' => $validatedData['kriteria'][$kuesionerId] ?? null,
                    'nilai_sebelum' => $nilai_sebelum,
                    'nilai_sesudah' => $nilai_sesudah,
                    'nilai_delta' => $nilai_delta,
                    'catatan' => $validatedData['catatan'][$kuesionerId] ?? null,
                    'aspek_id' => $validatedData['aspek_id'][$kuesionerId] ?? null,
                    'level' => $validatedData['level'][$kuesionerId] ?? null,
                    'remark' => $validatedData['remark'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $dataToInsertDB = array_map(function ($item) {
                unset($item['aspek_id'], $item['level'], $item['kriteria']);
                return $item;
            }, $dataToInsert);

            // Insert batch data ke dalam tabel project_jawaban_kuesioner
            DB::table('project_jawaban_kuesioner')->insert($dataToInsertDB);

            $averageByAspek = [];
            $countByAspek = [];
            $kriteriaByAspek = [];
            $levelByAspek = [];

            foreach ($dataToInsert as $item) {
                $aspekId = $item['aspek_id'];
                $kriteria = $item['kriteria'];
                $level = $item['level'];

                if ($aspekId !== null) {
                    if (!isset($averageByAspek[$aspekId])) {
                        $averageByAspek[$aspekId] = 0;
                        $countByAspek[$aspekId] = 0;
                        $kriteriaByAspek[$aspekId] = $kriteria;
                        $levelByAspek[$aspekId] = $level;
                    }
                    $averageByAspek[$aspekId] += $item['nilai_delta'];
                    $countByAspek[$aspekId]++;
                }
            }

            // Buat array hasil rata-rata dengan konversi dan bobot
            $averageData = [];

            foreach ($averageByAspek as $aspekId => $totalDelta) {
                $averageNilaiDelta = (int) round($totalDelta / $countByAspek[$aspekId]);
                $kriteria = $kriteriaByAspek[$aspekId] ?? null;

                // Penyesuaian kriteria untuk query konversi
                $jenisSkor = ($kriteria == 'Delta Skor Persepsi') ? '∆ Skor Persepsi' : $kriteria;

                // Ambil data konversi berdasarkan diklat_type_id, jenis_skor, dan skor
                $konversi = DB::table('konversi')
                    ->where('diklat_type_id', $diklatTypeId)
                    ->where('jenis_skor', $jenisSkor)
                    ->where('skor', $averageNilaiDelta)
                    ->value('konversi') ?? 0; // Jika tidak ditemukan, set default ke 0

                // Ambil bobot berdasarkan project_id dan aspek_id
                $bobotField = ($validatedData['remark'] == 'Alumni') ? 'bobot_alumni' : 'bobot_atasan_langsung';

                $bobot = DB::table('project_bobot_aspek')
                    ->where('project_id', $validatedData['project_id'])
                    ->where('aspek_id', $aspekId)
                    ->value($bobotField) ?? 0; // Jika tidak ditemukan, set default ke 0

                // Hitung nilai dengan rumus konversi * bobot / 100, dibulatkan 2 angka desimal
                $nilai = round(($konversi * $bobot) / 100, 2);

                $averageData[] = [
                    'diklat_type_id' => $diklatTypeId,
                    'aspek_id' => $aspekId,
                    'kriteria' => $kriteria,
                    'level' => $levelByAspek[$aspekId] ?? null,
                    'average_nilai_delta' => $averageNilaiDelta,
                    'konversi' => (int) $konversi,
                    'bobot' => $bobot,
                    'nilai' => $nilai,
                ];
            }

            $skorLevel3 = collect($averageData)
                ->where('level', '3')
                ->sum('nilai');

            $skorLevel4 = collect($averageData)
                ->where('level', '4')
                ->sum('nilai');

            $existingData = DB::table('project_skor_responden')
                ->where('project_id', $validatedData['project_id'])
                ->where('project_responden_id', $validatedData['project_responden_id'])
                ->first();

            if ($existingData) {
                // Jika data sudah ada, lakukan update
                if ($validatedData['remark'] == 'Alumni') {
                    DB::table('project_skor_responden')
                        ->where('id', $existingData->id)
                        ->update([
                            'log_data_alumni' => json_encode($averageData, JSON_PRETTY_PRINT),
                            'skor_level_3_alumni' => round($skorLevel3, 2),
                            'skor_level_4_alumni' => round($skorLevel4, 2),
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('project_skor_responden')
                        ->where('id', $existingData->id)
                        ->update([
                            'log_data_atasan' => json_encode($averageData, JSON_PRETTY_PRINT),
                            'skor_level_3_atasan' => round($skorLevel3, 2),
                            'skor_level_4_atasan' => round($skorLevel4, 2),
                            'updated_at' => now(),
                        ]);
                }
            } else {
                // Jika data belum ada, lakukan insert
                DB::table('project_skor_responden')->insert([
                    'project_id' => $validatedData['project_id'],
                    'project_responden_id' => $validatedData['project_responden_id'],
                    'log_data_alumni' => $validatedData['remark'] == 'Alumni' ? json_encode($averageData, JSON_PRETTY_PRINT) : null,
                    'skor_level_3_alumni' => $validatedData['remark'] == 'Alumni' ? round($skorLevel3, 2) : 0,
                    'skor_level_4_alumni' => $validatedData['remark'] == 'Alumni' ? round($skorLevel4, 2) : 0,
                    'log_data_atasan' => $validatedData['remark'] == 'Alumni' ? null : json_encode($averageData, JSON_PRETTY_PRINT),
                    'skor_level_3_atasan' => $validatedData['remark'] == 'Alumni' ? 0 : round($skorLevel3, 2),
                    'skor_level_4_atasan' => $validatedData['remark'] == 'Alumni' ? 0 : round($skorLevel4, 2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            // Menentukan field yang diupdate berdasarkan remark
            $updateData = [
                ($validatedData['remark'] === 'Alumni' ? 'status_pengisian_kuesioner_alumni' : 'status_pengisian_kuesioner_atasan') => 'Sudah'
            ];

            // Jika remark adalah Alumni, tambahkan update nama_atasan, telepon_atasan, dan deadline_pengisian_atasan
            if ($validatedData['remark'] === 'Alumni') {
                $updateData['nama_atasan'] = $validatedData['atasan'] ?? null;
                $updateData['telepon_atasan'] = $validatedData['no_wa'] ?? null;
                // Ambil nilai deadline dari tabel setting
                $setting = \App\Models\Setting::first();
                $deadlineDays = $setting ? (int) $setting->deadline_pengisian : 7; // Default ke 7 jika tidak ada data
                $updateData['deadline_pengisian_atasan'] = now()->addDays($deadlineDays)->format('Y-m-d');
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
