<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            return view('kuesioner', compact('responden', 'target', 'kuesioner', 'isExpired', 'sudahMengisi', 'encryptedId', 'token'));
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
            'encryptedId' => 'required',
            'token' => 'required',
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

            return redirect()->route('hasil-evaluasi-responden.index', [
                'id' => $validatedData['encryptedId'],
                'token' => $validatedData['token']
            ])->with('success', 'Jawaban berhasil disimpan dan status diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function hasilEvaluasi($encryptedId, Request $request)
    {
        try {
            $id = decryptShort($encryptedId); // ID dari project_responden
            $token = $request->query('token');

            // 1. Ambil data responden dan project
            $responden = DB::table('project_responden')
                ->join('project', 'project_responden.project_id', '=', 'project.id')
                ->select(
                    'project_responden.*', // Ambil semua kolom dari project_responden
                    'project.id as project_id',
                    'project.diklat_type_id'
                )
                ->where('project_responden.id', $id)
                ->where('project_responden.token', $token) // Validasi token
                ->first();

            if (!$responden) {
                abort(403, 'Data responden tidak ditemukan atau token tidak valid.');
            }

            $projectId = $responden->project_id;
            $diklatTypeId = $responden->diklat_type_id;

            // 2. Ambil data skor yang tersimpan
            $skorData = DB::table('project_skor_responden')
                ->where('project_responden_id', $id)
                ->first();

            if (!$skorData) {
                return redirect()->back()->with('error', 'Data skor evaluasi belum tersedia.');
            }

            // 3. Ambil detail perhitungan dari log
            $detailAlumni = json_decode($skorData->log_data_alumni, true) ?? [];
            $detailAtasan = json_decode($skorData->log_data_atasan, true) ?? [];

            // --- MODIFIKASI DIMULAI DI SINI ---

            // 3.1. Kumpulkan semua aspek_id unik dari detail log
            $allAspekIds = collect($detailAlumni)
                ->merge($detailAtasan) // Gabungkan data alumni dan atasan
                ->pluck('aspek_id')    // Ambil semua aspek_id
                ->filter()             // Hapus nilai null/kosong
                ->unique()             // Ambil ID yang unik
                ->values()
                ->all();

            // 3.2. Buat Peta Nama Aspek (Aspek ID => Nama Aspek)
            $aspekNamesMap = [];
            if (!empty($allAspekIds)) {
                // Query ke tabel 'aspek' untuk mendapatkan nama berdasarkan ID
                // Pastikan tabel 'aspek' memiliki kolom 'id' dan 'aspek' (nama)
                $aspekData = DB::table('aspek')
                    ->whereIn('id', $allAspekIds)
                    ->pluck('aspek', 'id'); // Hasilnya [id => nama_aspek]

                $aspekNamesMap = $aspekData->all();
            }

            // 3.3. Fungsi untuk menambahkan nama aspek ke array detail
            $injectAspectName = function ($details) use ($aspekNamesMap) {
                return collect($details)->map(function ($item) use ($aspekNamesMap) {
                    // Cari nama aspek berdasarkan aspek_id di peta
                    $item['aspek_nama'] = $aspekNamesMap[$item['aspek_id']] ?? 'Aspek Tidak Ditemukan';
                    return $item;
                })->all();
            };

            // 3.4. Tambahkan nama aspek ke data detail Alumni dan Atasan
            $detailAlumni = $injectAspectName($detailAlumni);
            $detailAtasan = $injectAspectName($detailAtasan);

            // 3.5 Pisahkan lagi berdasarkan level setelah nama ditambahkan
            $detailAlumniLevel3 = collect($detailAlumni)->where('level', '3')->values()->all();
            $detailAtasanLevel3 = collect($detailAtasan)->where('level', '3')->values()->all();
            $detailAlumniLevel4 = collect($detailAlumni)->where('level', '4')->values()->all();
            $detailAtasanLevel4 = collect($detailAtasan)->where('level', '4')->values()->all();

            // 4. Ambil data sekunder dan bobotnya untuk level 4
            $dataSekunder = DB::table('project_data_sekunder')
                ->where('project_id', $projectId)
                ->first();
            $bobotSekunderObj = DB::table('project_bobot_aspek_sekunder')
                ->where('project_id', $projectId)
                ->first();

            $nilaiSekunder = 0;
            if ($dataSekunder && $bobotSekunderObj && $dataSekunder->nilai_kinerja_akhir > $dataSekunder->nilai_kinerja_awal) {
                $nilaiSekunder = $bobotSekunderObj->bobot_aspek_sekunder ?? 0;
            }

            // 5. Hitung total skor
            $totalLevel3 = ($skorData->skor_level_3_alumni ?? 0) + ($skorData->skor_level_3_atasan ?? 0);
            $totalLevel4Primer = ($skorData->skor_level_4_alumni ?? 0) + ($skorData->skor_level_4_atasan ?? 0);
            $totalLevel4 = $totalLevel4Primer + $nilaiSekunder;
            // Batasi total skor maksimal 100
            $totalLevel3 = min(round($totalLevel3, 2), 100);
            $totalLevel4 = min(round($totalLevel4, 2), 100);


            // 6. Kirim data ke view
            return view('hasil_evaluasi_responden', compact(
                'responden',
                'detailAlumniLevel3',
                'detailAtasanLevel3',
                'detailAlumniLevel4',
                'detailAtasanLevel4',
                'skorData', // Mengirim objek skorData untuk akses mudah skor per level per remark
                'nilaiSekunder',
                'totalLevel3',
                'totalLevel4Primer', // Jika ingin menampilkan total primer sebelum ditambah sekunder
                'totalLevel4',
                'encryptedId', // Kirim kembali untuk referensi jika perlu
                'token' // Kirim kembali untuk referensi jika perlu
            ));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Tangani jika dekripsi gagal (ID tidak valid)
            abort(404, 'ID Responden tidak valid.');
        } catch (\Exception $e) {
            // Tangani error lainnya
            \Log::error('Error fetching evaluation results: ' . $e->getMessage()); // Log error
            abort(500, 'Terjadi kesalahan saat memuat hasil evaluasi.');
        }
    }
}
