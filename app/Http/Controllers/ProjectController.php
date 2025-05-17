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
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
                ->leftJoin('users', 'project.user_id', '=', 'users.id')
                ->join('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
                ->select(
                    'project.*',
                    'users.name as user_name',
                    'users.email',
                    'users.avatar',
                    'diklat_type.nama_diklat_type'
                )
                ->when(request('evaluator'), function ($query, $evaluator) {
                    $query->where('project.user_id', $evaluator);
                })
                ->when(request('diklat_type'), function ($query, $diklatType) {
                    $query->where('project.diklat_type_id', $diklatType);
                })
                ->orderBy('project.id', 'desc');
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
                    if (is_null($row->user_name) && is_null($row->email)) {
                        return '<div class="text-muted text-center">-</div>';
                    }
                    $userName = $row->user_name ?? '-';
                    $email = $row->email ?? '';
                    $avatar = $row->avatar
                        ? asset("storage/uploads/avatars/{$row->avatar}")
                        : "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "&s=450";
                    return '
                        <div class="d-flex align-items-center">
                            <img src="' . e($avatar) . '" class="img-thumbnail"
                                 style="width: 50px; height: 50px; border-radius: 5%; margin-right: 10px;">
                            <span>' . e($userName) . '</span>
                        </div>';
                })
                ->addColumn('action', 'project.include.action')
                ->rawColumns(['kuesioner', 'responden', 'bobot', 'user', 'wa', 'action'])
                ->toJson();
        }
        $evaluators = DB::table('users')
            ->select('id', 'name')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('project')
                    ->whereColumn('project.user_id', 'users.id');
            })
            ->orderBy('name')
            ->get();
        $diklatTypes = DB::table('diklat_type')
            ->select('id', 'nama_diklat_type')
            ->orderBy('nama_diklat_type')
            ->get();
        return view('project.index', compact('evaluators', 'diklatTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kaldikID'          => 'required|numeric',
            'diklatTypeName'   => 'required|string',
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
            // 1. Insert ke tabel project
            $diklatType = DB::table('diklat_type_mapping')
                ->where('diklatTypeName', $data['diklatTypeName'])
                ->first();
            if (!$diklatType) {
                throw new \Exception("Type diklat tidak di temukan");
            }
            $projectId = DB::table('project')->insertGetId([
                'diklat_type_id'    => $diklatType->diklat_type_id,
                'kode_project'      => $kode_project,
                'kaldikID'          => $data['kaldikID'],
                'diklatTypeName'    => $data['diklatTypeName'],
                'kaldikDesc'        => $data['kaldikDesc'],
                'user_id'           => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
            // 2. Insert data ke tabel project_kriteria_responden
            $kriteriaResponden = DB::table('kriteria_responden')
                ->where('diklat_type_id', $diklatType->diklat_type_id)
                ->first();
            if (!$kriteriaResponden) {
                throw new \Exception("No kriteria responden data found.");
            }
            // 3. Insert data ke tabel project_responden dari API
            $statusValues = json_decode($kriteriaResponden->nilai_post_test, true);
            $statusQuery = implode('&', array_map(fn($status) => 'status=' . urlencode($status), $statusValues));
            $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-peserta-diklat/{$data['kaldikID']}?" . http_build_query([
                'api_key' => config('services.pusdiklatwas.api_token'),
                'is_pagination' => 'No',
                'post_test_minimal' => $kriteriaResponden->nilai_post_test_minimal,
            ]) . "&" . $statusQuery;
            $response = Http::get($apiUrl);
            if ($response->failed()) {
                throw new \Exception("Gagal mengambil data responden dari API.");
            }
            $respondenData = $response->json();
            DB::table('project_kriteria_responden')->insert([
                'project_id'                        => $projectId,
                'nilai_post_test'                   => $kriteriaResponden->nilai_post_test,
                'nilai_post_test_minimal'           => $kriteriaResponden->nilai_post_test_minimal,
                'total_peserta'                     => $respondenData['total'],
                'total_termasuk_responden'          => $respondenData['total_include'],
                'total_tidak_termasuk_responden'    => $respondenData['total_exclude'],
                'created_at'                        => now(),
                'updated_at'                        => now(),
            ]);
            $insertData = [];
            if (isset($respondenData['data_include']) && is_array($respondenData['data_include'])) {
                foreach ($respondenData['data_include'] as $responden) {
                    $insertData[] = [
                        'project_id'       => $projectId,
                        'peserta_id'       => $responden['pesertaID'],
                        'nama'             => $responden['pesertaNama'],
                        'nip'              => $responden['pesertaNIP'],
                        'telepon'          => $responden['pesertaTelepon'],
                        'jabatan'          => trim($responden['jabatanFullName']),
                        'unit'            => $responden['unitName'],
                        'nilai_pre_test'   => $responden['pesertaNilaiPreTest'],
                        'nilai_post_test'  => $responden['pesertaNilaiPostTest'],
                        'token'            => Str::random(12),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }
            if (!empty($insertData)) {
                DB::table('project_responden')->insert($insertData);
            }
            $insertExcludeData = [];
            if (isset($respondenData['data_exclude']) && is_array($respondenData['data_exclude'])) {
                foreach ($respondenData['data_exclude'] as $responden) {
                    $insertExcludeData[] = [
                        'project_id'       => $projectId,
                        'peserta_id'       => $responden['pesertaID'],
                        'nama'             => $responden['pesertaNama'],
                        'nip'              => $responden['pesertaNIP'],
                        'telepon'          => $responden['pesertaTelepon'] ?? null,
                        'jabatan'          => trim($responden['jabatanFullName']),
                        'unit'             => $responden['unitName'],
                        'nilai_pre_test'   => $responden['pesertaNilaiPreTest'] ?? null,
                        'nilai_post_test'  => $responden['pesertaNilaiPostTest'] ?? null,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
                if (!empty($insertExcludeData)) {
                    DB::table('project_responden_exclude')->insert($insertExcludeData);
                }
            }
            // 4. Insert data ke tabel project_pesan_wa
            $pesanWa = DB::table('pesan_wa')->first();
            if (!$pesanWa) {
                throw new \Exception("Config pesan WA tidak ditemukan");
            }
            $textPesanAlumni = str_replace(
                ['{params_nama_diklat}'],
                [$data['kaldikDesc']],
                $pesanWa->text_pesan_alumni
            );
            $textPesanAtasan = str_replace(
                ['{params_nama_diklat}'],
                [$data['kaldikDesc']],
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
                ->where('aspek.diklat_type_id', $diklatType->diklat_type_id)
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
            $dataBobotSekunder = DB::table('bobot_aspek_sekunder')
                ->where('diklat_type_id', $diklatType->diklat_type_id)
                ->first();
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
            $kaldikDesc = $data['kaldikDesc'] ?? 'Pelatihan Default';
            $pertanyaanList = DB::table('kuesioner')
                ->join('aspek', 'kuesioner.aspek_id', '=', 'aspek.id')
                ->where('aspek.diklat_type_id', $diklatType->diklat_type_id)
                ->select('aspek.id as aspek_id', 'aspek.level', 'aspek.aspek', 'aspek.kriteria', 'kuesioner.pertanyaan')
                ->get();
            $kuesionerData = [];
            foreach ($pertanyaanList as $pertanyaanItem) {
                $pertanyaanAlumni = str_replace(
                    ["{params_target}", "{params_nama_diklat}"],
                    ["Saya", $kaldikDesc],
                    $pertanyaanItem->pertanyaan
                );
                $kuesionerData[] = [
                    'project_id'  => $projectId,
                    'aspek_id'    => $pertanyaanItem->aspek_id,
                    'level'       => $pertanyaanItem->level,
                    'aspek'       => $pertanyaanItem->aspek,
                    'kriteria'    => $pertanyaanItem->kriteria,
                    'remark'      => 'Alumni',
                    'pertanyaan'  => $pertanyaanAlumni,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
                $pertanyaanAtasan = str_replace(
                    ["{params_target}", "{params_nama_diklat}"],
                    ["Alumni", $kaldikDesc],
                    $pertanyaanItem->pertanyaan
                );
                $kuesionerData[] = [
                    'project_id'  => $projectId,
                    'aspek_id'    => $pertanyaanItem->aspek_id,
                    'level'       => $pertanyaanItem->level,
                    'aspek'       => $pertanyaanItem->aspek,
                    'kriteria'    => $pertanyaanItem->kriteria,
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
            ->leftJoin('users', 'project.user_id', '=', 'users.id')
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
        $aspeks = DB::table('aspek')
            ->select('id', 'aspek')
            ->where('diklat_type_id', $project->diklat_type_id)
            ->get();
        return view('project.kuesioner', compact('project', 'kuesioners', 'remark', 'aspeks'));
    }

    public function storeKuesioner(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required',
            'remark' => 'required',
            'aspek' => 'required',
            'pertanyaan' => 'required|string',
        ]);
        DB::beginTransaction();
        try {
            $dataAspek = DB::table('aspek')
                ->where('id', $request->aspek)
                ->first();
            if (!$dataAspek) {
                return back()->with('error', 'Aspek tidak ditemukan!');
            }
            DB::table('project_kuesioner')->insert([
                'project_id' => $validated['project_id'],
                'aspek_id' => $validated['aspek'],
                'remark' => $validated['remark'],
                'aspek' => $dataAspek->aspek,
                'kriteria' => $dataAspek->kriteria,
                'level' => $dataAspek->level,
                'pertanyaan' => $validated['pertanyaan'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return back()->with('success', 'Kuesioner berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function editKuesioner($id)
    {
        $kuesioner = DB::table('project_kuesioner')->where('id', $id)->first();
        return response()->json($kuesioner);
    }

    public function updateKuesioner(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $dataAspek = DB::table('aspek')
                ->where('id', $request->aspek)
                ->first();
            if (!$dataAspek) {
                return back()->with('error', 'Aspek tidak ditemukan!');
            }
            DB::table('project_kuesioner')
                ->where('id', $id)
                ->update([
                    'aspek_id' => $request->aspek,
                    'level' => $dataAspek->level,
                    'kriteria' => $dataAspek->kriteria,
                    'aspek' => $dataAspek->aspek,
                    'pertanyaan' => $request->pertanyaan,
                    'updated_at' => now(),
                ]);
            DB::commit();
            return back()->with('success', 'Kuesioner berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deleteKuesioner($id)
    {
        DB::table('project_kuesioner')->where('id', $id)->delete();
        return response()->json(['success' => 'Kuesioner berhasil dihapus']);
    }

    public function showResponden($id): View|JsonResponse
    {
        if (request()->ajax()) {
            $table = request()->get('table');
            if ($table === 'exclude') {
                $respondens = DB::table('project_responden_exclude')
                    ->where('project_id', $id)
                    ->get();
            } else {
                $respondens = DB::table('project_responden')
                    ->where('project_id', $id)
                    ->get();
            }
            return DataTables::of($respondens)
                ->addIndexColumn()
                ->toJson();
        }
        $kriteriaResponden = DB::table('project_kriteria_responden')
            ->where('project_id', $id)
            ->first();
        if (!$kriteriaResponden) {
            abort(404, 'Kriteria Responden tidak ditemukan');
        }
        $kriteriaResponden->nilai_post_test = json_decode($kriteriaResponden->nilai_post_test, true);
        $project = DB::table('project')
            ->leftJoin('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();
        return view('project.responden', compact('project', 'kriteriaResponden'));
    }

    public function updateResponden(Request $request, $id)
    {
        $request->validate([
            'nilai_post_test' => 'sometimes|array',
            'nilai_post_test.*' => 'in:Turun,Tetap,Naik',
            'nilai_post_test_minimal' => 'required|numeric',
        ]);
        DB::beginTransaction();
        try {
            $nilai_post_test =  $request->nilai_post_test ?? [];
            // Hapus data lama di `project_responden` berdasarkan `project_id`
            DB::table('project_responden')->where('project_id', $request->project_id)->delete();
            DB::table('project_responden_exclude')->where('project_id', $request->project_id)->delete();
            // Hit API untuk mendapatkan data responden
            $statusQuery = implode('&', array_map(fn($status) => 'status=' . urlencode($status), $nilai_post_test));
            $apiUrl = config('services.pusdiklatwas.endpoint') . "/len-peserta-diklat/{$request->kaldikID}?" . http_build_query([
                'api_key' => config('services.pusdiklatwas.api_token'),
                'is_pagination' => 'No',
                'post_test_minimal' => $request->nilai_post_test_minimal,
            ]) . "&" . $statusQuery;
            $response = Http::get($apiUrl);
            if ($response->failed()) {
                throw new \Exception("Gagal mengambil data responden dari API.");
            }
            $respondenData = $response->json();
            $updated = DB::table('project_kriteria_responden')
                ->where('id', $id)
                ->update([
                    'nilai_post_test' => json_encode($nilai_post_test), // Simpan dalam format JSON
                    'nilai_post_test_minimal' => $request->nilai_post_test_minimal,
                    'total_peserta'                     => $respondenData['total'],
                    'total_termasuk_responden'          => $respondenData['total_include'],
                    'total_tidak_termasuk_responden'    => $respondenData['total_exclude'],
                    'updated_at' => now(),
                ]);
            $insertData = [];
            if (isset($respondenData['data_include']) && is_array($respondenData['data_include'])) {
                foreach ($respondenData['data_include'] as $responden) {
                    $insertData[] = [
                        'project_id'         => $request->project_id,
                        'peserta_id'         => $responden['pesertaID'],
                        'nama'               => $responden['pesertaNama'],
                        'nip'                => $responden['pesertaNIP'],
                        'telepon'            => $responden['pesertaTelepon'],
                        'jabatan'            => trim($responden['jabatanFullName']),
                        'unit'               => $responden['unitName'],
                        'nilai_pre_test'     => $responden['pesertaNilaiPreTest'],
                        'nilai_post_test'    => $responden['pesertaNilaiPostTest'],
                        'token'            => Str::random(12),
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                }
            }
            if (!empty($insertData)) {
                DB::table('project_responden')->insert($insertData);
            }
            $insertExcludeData = [];
            if (isset($respondenData['data_exclude']) && is_array($respondenData['data_exclude'])) {
                foreach ($respondenData['data_exclude'] as $responden) {
                    $insertExcludeData[] = [
                        'project_id'       => $request->project_id,
                        'peserta_id'       => $responden['pesertaID'],
                        'nama'             => $responden['pesertaNama'],
                        'nip'              => $responden['pesertaNIP'],
                        'telepon'          => $responden['pesertaTelepon'] ?? null,
                        'jabatan'          => trim($responden['jabatanFullName']),
                        'unit'             => $responden['unitName'],
                        'nilai_pre_test'   => $responden['pesertaNilaiPreTest'] ?? null,
                        'nilai_post_test'  => $responden['pesertaNilaiPostTest'] ?? null,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
                if (!empty($insertExcludeData)) {
                    DB::table('project_responden_exclude')->insert($insertExcludeData);
                }
            }
            DB::commit();
            return back()->with($updated ? 'success' : 'error', $updated ? 'Kriteria responden berhasil diperbarui!' : 'Kriteria responden gagal diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showPesanWa($id)
    {
        $project = DB::table('project')
            ->leftJoin('users', 'project.user_id', '=', 'users.id')
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
            ->leftJoin('users', 'project.user_id', '=', 'users.id')
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

    public function updateStatus($id)
    {
        try {
            DB::beginTransaction(); // Mulai transaksi
            $project = DB::table('project')->where('id', $id)->first();
            if (!$project) {
                return to_route('project.index')->with('error', __('Project tidak ditemukan.'));
            }
            if ($project->status === 'Pelaksanaan') {
                return to_route('project.index')->with('error', __('Status sudah Pelaksanaan, tidak bisa diubah lagi.'));
            }
            $user = auth()->user();
            if (!$user) {
                throw new \Exception("User is not authenticated.");
            }
            // Ambil nilai deadline dari tabel setting
            $setting = \App\Models\Setting::first();
            $deadlineDays = $setting ? (int) $setting->deadline_pengisian : 7;
            $deadlineDate = now()->addDays($deadlineDays)->toDateString();
            // Ambil data pesan WA untuk project ini
            $pesanWa = DB::table('project_pesan_wa')->where('project_id', $project->id)->first();
            if ($pesanWa) {
                // Dummy kaldikDesc — sesuaikan dengan nilai yang sebenarnya dari proyek
                $data['kaldikDesc'] = $project->nama_diklat ?? 'Nama Diklat'; // Atur fallback jika tidak ada
                $textPesanAlumni = str_replace(
                    ['{params_wa_pic}', '{params_pic}'],
                    [$user->phone, $user->name],
                    $pesanWa->text_pesan_alumni
                );
                $textPesanAtasan = str_replace(
                    ['{params_wa_pic}', '{params_pic}'],
                    [$user->phone, $user->name],
                    $pesanWa->text_pesan_atasan
                );
                // Kalau mau disimpan kembali, bisa update:
                DB::table('project_pesan_wa')
                    ->where('project_id', $project->id)
                    ->update([
                        'text_pesan_alumni' => $textPesanAlumni,
                        'text_pesan_atasan' => $textPesanAtasan,
                    ]);
            }
            // Update status proyek + user login yang mengubah
            $updated = DB::table('project')
                ->where('id', $id)
                ->update([
                    'status' => 'Pelaksanaan',
                    'user_id' => $user->id
                ]);
            if (!$updated) {
                throw new \Exception("Gagal mengupdate status proyek.");
            }
            // Update deadline_pengisian_alumni
            $respondensUpdated = DB::table('project_responden')
                ->where('project_id', $id)
                ->update(['deadline_pengisian_alumni' => $deadlineDate]);
            if ($respondensUpdated === 0) {
                throw new \Exception("Gagal mengupdate deadline pengisian alumni.");
            }
            DB::commit();
            return to_route('project.index')->with('success', __('Status berhasil diperbarui menjadi Pelaksanaan.'));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal mengupdate status proyek: ' . $e->getMessage());
            return to_route('project.index')->with('error', __('Terjadi kesalahan: ') . $e->getMessage());
        }
    }

    public function exportPdf($id)
    {
        try {
            // 1. Ambil data project utama
            $project = DB::table('project')
                ->leftJoin('users', 'project.user_id', '=', 'users.id')
                ->join('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
                ->select(
                    'project.*',
                    'users.name as user Mendes, Julia; Ristanti, Rika A.; Widodo, Joko; Subowo, Agus; Lestari, Tika; Nurhayati, Ani; Santoso, Budi; Hartono, Dedy; Pratama, Eko; Wulandari, Fitri name',
                    'diklat_type.nama_diklat_type'
                )
                ->where('project.id', $id)
                ->first();
            if (!$project) {
                Log::warning('Gagal generate PDF: Project tidak ditemukan - ID ' . $id);
                return redirect()->route('project.index')->with('error', 'Project tidak ditemukan.');
            }
            // Parse tanggal created_at jika ada
            $projectCreatedAt = $project->created_at ? Carbon::parse($project->created_at)->format('Y-m-d') : 'N/A';
            // 2. Ambil Data Kriteria Responden (Section A)
            $kriteriaResponden = DB::table('project_kriteria_responden')
                ->where('project_id', $id)
                ->first();
            // Decode JSON kriteria nilai post test
            $kriteriaNilaiPostTest = [];
            if ($kriteriaResponden && is_string($kriteriaResponden->nilai_post_test)) {
                $decoded = json_decode($kriteriaResponden->nilai_post_test, true);
                if (is_array($decoded)) {
                    $kriteriaNilaiPostTest = $decoded;
                }
            }
            // !!! NOTE: Asumsi ada kolom 'nilai_pre_test_minimal' di tabel project_kriteria_responden
            //     Jika tidak ada, variabel ini akan null.
            // 4. Ambil Data Daftar Responden Alumni (Section C)
            $daftarResponden = DB::table('project_responden')
                ->where('project_id', $id)
                ->select('nip', 'nama', 'jabatan', 'telepon', 'unit', /* 'pangkat' -> tidak ada di tabel? */)
                ->orderBy('nama') // Urutkan berdasarkan nama
                ->get();
            // 5. Ambil Data Kuesioner (Section D)
            $kuesionerAlumni = DB::table('project_kuesioner')
                ->join('aspek', 'project_kuesioner.aspek_id', '=', 'aspek.id')
                ->select(
                    'project_kuesioner.pertanyaan',
                    'aspek.aspek as aspek_nama',
                    'aspek.kriteria',
                    DB::raw("CONCAT('KU', DATE_FORMAT(project_kuesioner.created_at, '%m%d%H%i%s')) as kode_kuesioner") // Contoh Kode Kuesioner
                )
                ->where('project_kuesioner.project_id', $id)
                ->where('project_kuesioner.remark', 'Alumni')
                ->orderBy('project_kuesioner.id')
                ->get();
            $kuesionerAtasan = DB::table('project_kuesioner')
                ->join('aspek', 'project_kuesioner.aspek_id', '=', 'aspek.id')
                ->select(
                    'project_kuesioner.pertanyaan',
                    'aspek.aspek as aspek_nama',
                    'aspek.kriteria',
                    DB::raw("CONCAT('KU', DATE_FORMAT(project_kuesioner.created_at, '%m%d%H%i%s')) as kode_kuesioner") // Contoh Kode Kuesioner
                )
                ->where('project_kuesioner.project_id', $id)
                ->where('project_kuesioner.remark', 'Atasan')
                ->orderBy('project_kuesioner.id')
                ->get();
            // 6. Ambil Data Bobot (Section E)
            $bobotAspek = DB::table('project_bobot_aspek')
                ->where('project_id', $id)
                ->orderBy('level')
                ->orderBy('id') // Urutkan berdasarkan level dan id
                ->get();
            $bobotLevel3 = $bobotAspek->where('level', 3);
            $bobotLevel4 = $bobotAspek->where('level', 4);
            $bobotSekunder = DB::table('project_bobot_aspek_sekunder')
                ->where('project_id', $id)
                ->first();
            // 7. Data Tambahan (Logo, Tanggal Cetak)
            $setting = Setting::first();
            $logoPath = null; // Default null
            $logoUrl = null;  // Default null
            // Coba ambil logo dari setting database
            if ($setting && $setting->logo_instansi) {
                $dbLogoPath = public_path('storage/uploads/logos/' . $setting->logo_instansi); // Sesuaikan path storage Anda
                if (file_exists($dbLogoPath)) {
                    $logoPath = $dbLogoPath; // Gunakan logo dari DB jika ada dan file-nya eksis
                }
            }
            // Jika logo dari DB tidak ditemukan/tidak valid, coba fallback ke logo statis
            if (!$logoPath) {
                $staticLogoPath = public_path('assets/BPKP_Logo.png'); // Path logo statis di public/assets
                if (file_exists($staticLogoPath)) {
                    $logoPath = $staticLogoPath; // Gunakan logo statis jika ada
                } else {
                    Log::warning('Logo dinamis maupun statis (public/assets/BPKP_Logo.png) tidak ditemukan.');
                }
            }
            // Encode logo ke base64 jika path valid
            if ($logoPath) {
                try {
                    $logoUrl = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                } catch (\Exception $e) {
                    Log::error('Gagal membaca file logo: ' . $logoPath . ' - Error: ' . $e->getMessage());
                    $logoUrl = null; // Set null jika gagal baca file
                }
            }
            $tanggalCetak = Carbon::now()->translatedFormat('d F Y H:i');
            // 8. Siapkan data untuk view
            $data = [
                'project' => $project,
                'projectCreatedAt' => $projectCreatedAt,
                'kriteriaResponden' => $kriteriaResponden,
                'kriteriaNilaiPostTest' => $kriteriaNilaiPostTest,
                'daftarResponden' => $daftarResponden, // Data Section C
                'kuesionerAlumni' => $kuesionerAlumni, // Data Section D.a
                'kuesionerAtasan' => $kuesionerAtasan, // Data Section D.b
                'bobotLevel3' => $bobotLevel3,         // Data Section E (Level 3)
                'bobotLevel4' => $bobotLevel4,         // Data Section E (Level 4)
                'bobotSekunder' => $bobotSekunder,     // Data Section E (Sekunder)
                'logoUrl' => $logoUrl,
                'tanggalCetak' => $tanggalCetak,
            ];
            // 9. Generate PDF menggunakan view baru (misal: 'project.export-pdf')
            // Kita akan buat view ini di langkah berikutnya
            $pdf = Pdf::loadView('project.export-pdf', $data); // <-- Nama view baru!
            $pdf->setPaper('a4', 'portrait');
            // 10. Atur nama file & kirim ke browser
            $filename = 'Penyebaran-Kuesioner-' . Str::slug($project->kaldikDesc ?? 'project') . '-' . $project->kaldikID . '.pdf';
            return $pdf->stream($filename); // Tampilkan di browser
        } catch (\Exception $e) {
            Log::error('Error generating PDF Management Project ID ' . $id . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('project.index')->with('error', 'Gagal membuat PDF: Terjadi kesalahan internal. Silakan cek log.');
        }
    }
}
