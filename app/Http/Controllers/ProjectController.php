<?php

namespace App\Http\Controllers;

use App\Models\KriteriaResponden;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $projects = DB::table('project')
                ->join('users', 'project.user_id', '=', 'users.id')
                ->select('project.*', 'users.name as user_name', 'users.email', 'users.avatar');

            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('kuesioner', function ($row) {
                    $editAlumniUrl = route('kuesioner.show', ['id' => $row->id, 'remark' => 'Alumni']);
                    $editAtasanUrl = route('kuesioner.show', ['id' => $row->id, 'remark' => 'Atasan']);
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

                ->addColumn('peserta', function ($row) {
                    $editResponden = route('responden.show', ['id' => $row->id]);
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
                    return '
                        <div class="text-center">
                            <a href="#"
                               class="btn btn-sm btn-danger"
                               style="width: 140px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                <i class="fas fa-balance-scale"></i> Atur Botot
                            </a>
                        </div>';
                })

                ->addColumn('wa', function ($row) {
                    $editWa = route('pesan.wa.show', ['id' => $row->id]);
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
                ->addColumn('action', 'project.include.action')
                ->rawColumns(['kuesioner', 'peserta', 'bobot', 'user', 'wa', 'action'])
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

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Cek apakah kaldikID sudah ada di dalam tabel project
            $existingProject = DB::table('project')->where('kaldikID', $data['kaldikID'])->first();

            if ($existingProject) {
                return response()->json([
                    'status'  => false,
                    'message' => "Project with Kaldik ID {$data['kaldikID']} already exists in project management.",
                ], 409);
            }

            $kode_project = Str::upper(Str::random(8));
            $userId = auth()->id();

            // 1. Insert ke tabel project
            $projectId = DB::table('project')->insertGetId([
                'kode_project'  => $kode_project,
                'kaldikID'      => $data['kaldikID'],
                'kaldikDesc'    => $data['kaldikDesc'],
                'user_id'       => $userId, // Simpan ID user yang login
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 2. Insert data ke table kriteria_responden
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

            // 3. Insert data ke table project_pesan_wa
            $pesanWa = DB::table('pesan_wa')->first();

            if (!$pesanWa) {
                throw new \Exception("Config pesan WA tidak di temukan");
            }

            DB::table('project_pesan_wa')->insert([
                'project_id'              => $projectId,
                'text_pesan_alumni'       => $pesanWa->text_pesan_alumni,
                'text_pesan_atasan'       => $pesanWa->text_pesan_atasan,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            // 4. Ambil 5 data pertama dari tabel aspek
            $aspekList = DB::table('aspek')->limit(5)->get();

            if ($aspekList->isEmpty()) {
                throw new \Exception("No aspek data found.");
            }

            // Ambil deskripsi pelatihan dari parameter
            $kaldikDesc = $data['kaldikDesc'] ?? 'Pelatihan Default';

            // Daftar pertanyaan dengan placeholder
            $pertanyaanList = [
                1 => "{params_target} termotivasi untuk terlibat secara aktif dalam setiap penugasan yang relevan dengan pelatihan ini.",
                2 => "{params_target} percaya diri untuk terlibat secara aktif dalam setiap kegiatan yang relevan dengan pelatihan ini.",
                3 => "Setelah mengikuti pelatihan, {params_target} berbagi pengetahuan yang telah diperoleh selama pelatihan kepada rekan-rekan kerja melalui kegiatan pelatihan di kantor sendiri, FGD, sharing session, atau bentuk knowledge sharing lainnya.",
                4 => "{params_target} mampu menerapkan ilmu yang telah diperoleh selama Pelatihan {$kaldikDesc} pada setiap penugasan yang relevan.",
                5 => "Implementasi hasil pelatihan ini berdampak positif dalam meningkatkan pengelolaan keuangan negara."
            ];

            // STEP 2: Buat array untuk batch insert ke project_kuesioner
            $kuesionerData = [];

            foreach ($aspekList as $aspek) {
                // Tentukan kriteria berdasarkan nilai field `aspek`
                $kriteria = ($aspek->aspek === "Kemampuan Membagikan Keilmuan") ? 'Skor Persepsi' : 'Delta Skor Persepsi';

                // Cek apakah aspek_id ada di daftar pertanyaan
                $pertanyaanTemplate = $pertanyaanList[$aspek->id] ?? "Pertanyaan default untuk aspek ID {$aspek->id}";

                // Data untuk Alumni (ganti {params_target} → "Saya")
                $pertanyaanAlumni = str_replace("{params_target}", "Saya", $pertanyaanTemplate);
                $kuesionerData[] = [
                    'project_id'  => $projectId,
                    'aspek_id'    => $aspek->id,
                    'kriteria'    => $kriteria,
                    'remark'      => 'Alumni',
                    'pertanyaan'  => $pertanyaanAlumni,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];

                // Data untuk Atasan (ganti {params_target} → "Alumni")
                $pertanyaanAtasan = str_replace("{params_target}", "Alumni", $pertanyaanTemplate);
                $kuesionerData[] = [
                    'project_id'  => $projectId,
                    'aspek_id'    => $aspek->id,
                    'kriteria'    => $kriteria,
                    'remark'      => 'Atasan',
                    'pertanyaan'  => $pertanyaanAtasan,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            // STEP 3: Insert batch ke project_kuesioner
            DB::table('project_kuesioner')->insert($kuesionerData);


            // Commit transaksi jika semua proses sukses
            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Project created successfully',
                'data'    => [
                    'kode_project' => $kode_project,
                    'kaldikID'     => $data['kaldikID'],
                    'kaldikDesc'   => $data['kaldikDesc'],
                ],
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Failed to create project: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $deleted = DB::table('project')->where('id', $id)->delete();

            if ($deleted) {
                return to_route('project.index')->with('success', __('The project was deleted successfully.'));
            } else {
                return to_route('project.index')->with('error', __("The project was not found or couldn't be deleted."));
            }
        } catch (\Exception $e) {
            return to_route('project.index')->with('error', __("The project can't be deleted because it's related to another table."));
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
}
