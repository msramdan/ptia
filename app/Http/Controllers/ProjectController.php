<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ProjectController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:project view', only: ['index', 'show']),
            new Middleware('permission:generate project', only: ['store']),
            new Middleware('permission:project print', only: ['print']),
            new Middleware('permission:project delete', only: ['destroy']),
        ];
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $projects = DB::table('project')
                ->join('users', 'project.user_id', '=', 'users.id')
                ->select('project.*', 'users.name as user_name', 'users.email', 'users.avatar')
                ->orderBy('project.id', 'desc') // Urutkan berdasarkan ID secara descending
                ->get();
            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('kuesioner', function ($row) {
                    $editAlumniUrl = route('project.kuesioner.show', ['id' => $row->id, 'remark' => 'Alumni']);
                    $editAtasanUrl = route('project.kuesioner.show', ['id' => $row->id, 'remark' => 'Atasan']);
                    return '
                        <div class="text-center">
                            <a href="' . $editAlumniUrl . '"
                               class="btn btn-sm btn-primary"
                               style="width: 100px; background: #007bff; border-color: #007bff;"
                               data-toggle="tooltip" data-placement="left" title="Alumni: Ubah Kuesioner">
                                <i class="fas fa-clipboard-list"></i> Alumni
                            </a>
                            <br><hr style="margin: 5px;">
                            <a href="' . $editAtasanUrl . '"
                               class="btn btn-sm btn-secondary"
                               style="width: 100px; background: #6c757d; border-color: #6c757d;"
                               data-toggle="tooltip" data-placement="left" title="Atasan Langsung: Ubah Kuesioner">
                                <i class="fas fa-clipboard-list"></i> Atasan
                            </a>
                        </div>';
                })

                ->addColumn('responden', function ($row) {
                    $editResponden = route('project.responden.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                            <a href="' . $editResponden . '" class="btn btn-sm btn-warning"
                               style="width: 140px; background: #ffc107; border-color: #ffc107;"
                               data-toggle="tooltip" data-placement="left" title="Atur Responden">
                                <i class="fas fa-users-cog"></i> Atur Responden
                            </a>
                        </div>';
                })
                ->addColumn('bobot', function ($row) {
                    $editBobot = route('project.bobot.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                             <a href="' . $editBobot . '"
                               class="btn btn-sm btn-danger"
                               style="width: 140px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                <i class="fas fa-balance-scale"></i> Atur Botot
                            </a>
                        </div>';
                })

                ->addColumn('wa', function ($row) {
                    $editWa = route('project.pesan.wa.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                            <a href="' . $editWa . '"
                               class="btn btn-sm btn-success"
                               style="width: 140px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Pesan">
                                <i class="fab fa-whatsapp"></i> Atur Pesan
                            </a>
                        </div>';
                })

                ->addColumn('user', function ($row) {
                    $avatar = $row->avatar
                        ? asset("storage/uploads/avatars/$row->avatar")
                        : "https://www.gravatar.com/avatar/" . md5(strtolower(trim($row->email))) . "&s=450";

                    return '
                        <div class="d-flex align-items-center">
                            <img src="' . e($avatar) . '" class="img-thumbnail"
                                 style="width: 50px; height: 50px; border-radius: 5%; margin-right: 10px;">
                            <span>' . e($row->user_name) . '</span>
                        </div>';
                })
                ->rawColumns(['kuesioner', 'responden', 'bobot', 'user', 'wa'])
                ->toJson();
        }

        return view('project.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kaldikID'   => 'required|numeric',
            'kaldikDesc' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $existingProject = DB::table('project')->where('kaldikID', $data['kaldikID'])->first();

            if ($existingProject) {
                return response()->json([
                    'status'  => false,
                    'message' => "Kaldik ID {$data['kaldikID']} sudah ada dalam manajemen project.",

                ], 409);
            }

            $kode_project = Str::upper(Str::random(8));
            $user = auth()->user();

            if (!$user) {
                throw new \Exception("User is not authenticated.");
            }

            // 1. Insert ke tabel project
            $projectId = DB::table('project')->insertGetId([
                'kode_project'  => $kode_project,
                'kaldikID'      => $data['kaldikID'],
                'kaldikDesc'    => $data['kaldikDesc'],
                'user_id'       => $user->id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 2. Insert data ke tabel project_kriteria_responden
            $kriteriaResponden = DB::table('kriteria_responden')->first();

            if (!$kriteriaResponden) {
                throw new \Exception("No kriteria responden data found.");
            }

            DB::table('project_kriteria_responden')->insert([
                'project_id'              => $projectId,
                'nilai_post_test'         => $kriteriaResponden->nilai_post_test,
                'nilai_pre_test_minimal'  => $kriteriaResponden->nilai_pre_test_minimal,
                'nilai_post_test_minimal' => $kriteriaResponden->nilai_post_test_minimal,
                'nilai_kenaikan_pre_post' => $kriteriaResponden->nilai_kenaikan_pre_post,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            // 3. Insert data ke tabel project_responden dari API
            $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-peserta-diklat/{$data['kaldikID']}";
            $apiToken = config('services.pusdiklatwas.api_token');

            $response = Http::get($apiUrl, [
                'api_key' => $apiToken,
                'is_pagination' => 'No',
            ]);

            if ($response->failed()) {
                throw new \Exception("Gagal mengambil data responden dari API.");
            }

            $respondenData = $response->json();

            if (!isset($respondenData['data']) || !is_array($respondenData['data'])) {
                throw new \Exception("Format data responden dari API tidak valid.");
            }

            $insertData = [];

            foreach ($respondenData['data'] as $responden) {
                $insertData[] = [
                    'project_id'              => $projectId,
                    'peserta_id' => $responden['pesertaID'],
                    'nama' => $responden['pesertaNama'],
                    'nip' => $responden['pesertaNIP'],
                    'telepon' => $responden['pesertaTelepon'],
                    'jabatan' => trim($responden['jabatanFullName']),
                    'unit' => $responden['unitName'],
                    'nilai_pre_test' => $responden['pesertaNilaiPreTest'],
                    'nilai_post_test' => $responden['pesertaNilaiPostTest'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert batch untuk efisiensi
            if (!empty($insertData)) {
                DB::table('project_responden')->insert($insertData);
            }

            // 4. Insert data ke tabel project_pesan_wa
            $pesanWa = DB::table('pesan_wa')->first();

            if (!$pesanWa) {
                throw new \Exception("Config pesan WA tidak ditemukan");
            }

            $textPesanAlumni = str_replace(
                ['{params_judul_diklat}', '{params_wa_pic}', '{params_pic}'],
                [$data['kaldikDesc'], $user->phone, $user->name],
                $pesanWa->text_pesan_alumni
            );

            $textPesanAtasan = str_replace(
                ['{params_judul_diklat}', '{params_wa_pic}', '{params_pic}'],
                [$data['kaldikDesc'], $user->phone, $user->name],
                $pesanWa->text_pesan_atasan
            );

            DB::table('project_pesan_wa')->insert([
                'project_id'        => $projectId,
                'text_pesan_alumni' => $textPesanAlumni,
                'text_pesan_atasan' => $textPesanAtasan,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // 5.Insert data ke table project_bobot_aspek
            $dataBobot = DB::table('bobot_aspek')
                ->join('aspek', 'bobot_aspek.aspek_id', '=', 'aspek.id')
                ->select('bobot_aspek.aspek_id', 'bobot_aspek.bobot_alumni', 'bobot_aspek.bobot_atasan_langsung', 'aspek.level', 'aspek.aspek')
                ->get();

            if ($dataBobot->isEmpty()) {
                throw new \Exception("No bobot aspek found.");
            }

            $insertData = $dataBobot->map(function ($item) use ($projectId) {
                return [
                    'project_id' => $projectId,
                    'aspek_id' => $item->aspek_id,
                    'level' => $item->level,
                    'aspek' => $item->aspek,
                    'bobot_alumni' => $item->bobot_alumni,
                    'bobot_atasan_langsung' => $item->bobot_atasan_langsung,
                ];
            })->toArray();
            DB::table('project_bobot_aspek')->insert($insertData);

            // 6.insert data ke table project_bobot_aspek_sekunder
            $dataBobotSekunder = DB::table('bobot_aspek_sekunder')->first();

            if (!$dataBobotSekunder) {
                throw new \Exception("No bobot aspek sekunder found.");
            }

            DB::table('project_bobot_aspek_sekunder')->insert([
                'project_id' => $projectId,
                'bobot_aspek_sekunder' => $dataBobotSekunder->bobot_aspek_sekunder,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 7. Insert data ke table project_kuesioner
            $aspekList = DB::table('aspek')->limit(5)->get();

            if ($aspekList->isEmpty()) {
                throw new \Exception("No aspek data found.");
            }

            $kaldikDesc = $data['kaldikDesc'] ?? 'Pelatihan Default';

            $apiUrl = config('services.tna.endpoint') . "/pengajuan-kap";
            $apiKey = config('services.tna.api_token');
            $kodePembelajaran = $data['kaldikID'];

            $response = Http::get($apiUrl, [
                'api_key' => $apiKey,
                'kode_pembelajaran' => $kodePembelajaran,
            ]);

            $indikatorText = "kinerja organisasi";

            if ($response->successful() && isset($response['data']['indikatorKinerja'])) {
                $indikatorList = array_column($response['data']['indikatorKinerja'], 'indikator_kinerja');

                if (!empty($indikatorList)) {
                    // Tambahkan strip di setiap indikator dan gunakan <br> sebagai pemisah
                    $indikatorList = array_map(fn($item) => "- " . trim($item), $indikatorList);
                    $indikatorText = ":<br>" . implode("<br>", $indikatorList);
                }
            }

            $pertanyaanList = [
                1 => "{params_target} termotivasi untuk terlibat secara aktif dalam setiap penugasan yang relevan dengan pelatihan ini.",
                2 => "{params_target} percaya diri untuk terlibat secara aktif dalam setiap kegiatan yang relevan dengan pelatihan ini.",
                3 => "Setelah mengikuti pelatihan, {params_target} berbagi pengetahuan yang telah diperoleh selama pelatihan kepada rekan-rekan kerja melalui kegiatan pelatihan di kantor sendiri, FGD, sharing session, atau bentuk knowledge sharing lainnya.",
                4 => "{params_target} mampu menerapkan ilmu yang telah diperoleh selama Pelatihan {$kaldikDesc} pada setiap penugasan yang relevan.",
                5 => "Implementasi hasil pelatihan ini berdampak positif dalam meningkatkan {$indikatorText}"
            ];

            $kuesionerData = [];

            foreach ($aspekList as $aspek) {
                $pertanyaanTemplate = $pertanyaanList[$aspek->id] ?? "Pertanyaan default untuk aspek ID {$aspek->id}";
                $pertanyaanAlumni = str_replace("{params_target}", "Saya", $pertanyaanTemplate);
                $kuesionerData[] = [
                    'project_id'  => $projectId,
                    'aspek_id'    => $aspek->id,
                    'level'       => $aspek->level,
                    'aspek'       => $aspek->aspek,
                    'kriteria'    => $aspek->kriteria,
                    'remark'      => 'Alumni',
                    'pertanyaan'  => $pertanyaanAlumni,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];

                $pertanyaanAtasan = str_replace("{params_target}", "Alumni", $pertanyaanTemplate);
                $kuesionerData[] = [
                    'project_id'  => $projectId,
                    'aspek_id'    => $aspek->id,
                    'level'       => $aspek->level,
                    'aspek'       => $aspek->aspek,
                    'kriteria'    => $aspek->kriteria,
                    'remark'      => 'Atasan',
                    'pertanyaan'  => $pertanyaanAtasan,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
            DB::table('project_kuesioner')->insert($kuesionerData);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Project berhasil dibuat',
                'data'    => [
                    'kode_project' => $kode_project,
                    'kaldikID'     => $data['kaldikID'],
                    'kaldikDesc'   => $data['kaldikDesc'],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Gagal membuat project: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $deleted = DB::table('project')->where('id', $id)->delete();

            if ($deleted) {
                return to_route('project.index')->with('success', __('Project berhasil dihapus.'));
            } else {
                return to_route('project.index')->with('error', __("Project tidak ditemukan atau tidak dapat dihapus."));
            }
        } catch (\Exception $e) {
            return to_route('project.index')->with('error', __("Project tidak dapat dihapus karena terkait dengan tabel lain."));
        }
    }

    public function showKuesioner($id, $remark)
    {
        $project = DB::table('project')
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();

        $kuesioners = DB::table('project_kuesioner')
            ->join('aspek', 'project_kuesioner.aspek_id', '=', 'aspek.id')
            ->select(
                'project_kuesioner.*',
                'aspek.aspek as aspek_nama'
            )
            ->where('project_kuesioner.project_id', $id)
            ->where('project_kuesioner.remark', $remark)
            ->get();

        $aspeks = DB::table('aspek')->select('id', 'aspek')->get();

        return view('project.kuesioner', compact('project', 'kuesioners', 'remark', 'aspeks'));
    }

    public function editKuesioner($id)
    {
        $kuesioner = DB::table('project_kuesioner')->where('id', $id)->first();
        return response()->json($kuesioner);
    }

    public function updateKuesioner($id)
    {
        $kuesioner = DB::table('project_kuesioner')->where('id', $id)->first();

        return view('project.edit_kuesioner', compact('kuesioner'));
    }

    public function saveKuesioner(Request $request, $id)
    {
        DB::table('project_kuesioner')
            ->where('id', $id)
            ->update([
                'aspek_id' => $request->aspek,
                'kriteria' => $request->kriteria,
                'pertanyaan' => $request->pertanyaan,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Kuesioner berhasil diperbarui!');
    }

    public function deleteKuesioner($id)
    {
        DB::table('project_kuesioner')->where('id', $id)->delete();
        return response()->json(['success' => 'Kuesioner berhasil dihapus']);
    }

    public function tambahKuesioner(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required',
            'remark' => 'required',
            'aspek' => 'required',
            'kriteria' => 'required|string',
            'pertanyaan' => 'required|string',
        ]);

        // Menggunakan query builder untuk menyimpan data
        DB::table('project_kuesioner')->insert([
            'project_id' => $validated['project_id'],
            'aspek_id' => $validated['aspek'],
            'remark' => $validated['remark'],
            'kriteria' => $validated['kriteria'],
            'pertanyaan' => $validated['pertanyaan'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Kuesioner berhasil ditambahkan!');
    }

    public function showResponden($id)
    {
        $kriteriaResponden = DB::table('project_kriteria_responden')
            ->where('project_id', $id)
            ->first();

        if (!$kriteriaResponden) {
            abort(404, 'Kriteria Responden tidak ditemukan');
        }

        $kriteriaResponden->nilai_post_test = json_decode($kriteriaResponden->nilai_post_test, true);

        $project = DB::table('project')
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();

        return view('project.responden', compact('project', 'kriteriaResponden'));
    }

    public function showPesanWa($id)
    {
        $project = DB::table('project')
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();

        $pesanWa = DB::table('project_pesan_wa')
            ->where('project_pesan_wa.project_id', $id)
            ->first();
        return view('project.pesan_wa', compact('project', 'pesanWa'));
    }

    public function updatePesanWa(Request $request, $id)
    {
        $request->validate([
            'text_pesan_alumni' => 'required|string',
            'text_pesan_atasan' => 'required|string',
        ]);

        $updated = DB::table('project_pesan_wa')
            ->where('id', $id)
            ->update([
                'text_pesan_alumni' => $request->text_pesan_alumni,
                'text_pesan_atasan' => $request->text_pesan_atasan,
                'updated_at' => now(),
            ]);

        return back()->with($updated ? 'success' : 'error', $updated ? 'Pesan WA berhasil diperbarui!' : 'Pesan WA gagal diperbarui!');
    }

    public function showBobot($id)
    {
        $project = DB::table('project')
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();

        $bobotAspek = DB::table('project_bobot_aspek')
            ->join('aspek', 'project_bobot_aspek.aspek_id', '=', 'aspek.id')
            ->select('project_bobot_aspek.*', 'aspek.aspek as aspek_nama', 'aspek.level')
            ->where('project_bobot_aspek.project_id', $id)
            ->get();

        $dataSecondary = DB::table('project_bobot_aspek_sekunder')
            ->select('project_bobot_aspek_sekunder.*')
            ->where('project_bobot_aspek_sekunder.project_id', $id)
            ->first();

        $level3 = $bobotAspek->where('level', 3);
        $level4 = $bobotAspek->where('level', 4);

        return view('project.bobot', compact('project', 'level3', 'level4', 'dataSecondary'));
    }

    public function updateBobot(Request $request)
    {
        DB::transaction(function () use ($request) {
            // Update Level 3
            if ($request->has('level3')) {
                foreach ($request->level3 as $data) {
                    if (!empty($data['id'])) {
                        DB::table('project_bobot_aspek')->where('id', $data['id'])->update([
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
                        DB::table('project_bobot_aspek')->where('id', $data['id'])->update([
                            'bobot_alumni' => $data['bobot_alumni'] ?? 0,
                            'bobot_atasan_langsung' => $data['bobot_atasan_langsung'] ?? 0,
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // Update project_bobot_aspek_sekunder
            if ($request->has('bobot_aspek_sekunder_id') && !empty($request->bobot_aspek_sekunder_id)) {
                DB::table('project_bobot_aspek_sekunder')->where('id', $request->bobot_aspek_sekunder_id)->update([
                    'bobot_aspek_sekunder' => $request->bobot_aspek_sekunder ?? 0,
                    'updated_at' => now()
                ]);
            }
        });

        return redirect()->back()->with('success', 'Bobot aspek berhasil diperbarui.');
    }
}
