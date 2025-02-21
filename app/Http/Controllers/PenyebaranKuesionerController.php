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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class PenyebaranKuesionerController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:penyebaran kuesioner view', only: ['index']),
        ];
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $projects = DB::table('project')
                ->join('users', 'project.user_id', '=', 'users.id')
                ->leftJoin('project_responden', 'project_responden.project_id', '=', 'project.id')
                ->select(
                    'project.*',
                    'users.name as user_name',
                    'users.email',
                    'users.avatar',
                    // Total responden (semua)
                    DB::raw('COUNT(project_responden.id) as total_responden'),
                    // Total yang sudah mengisi kuesioner alumni
                    DB::raw('SUM(CASE WHEN project_responden.status_pengisian_kuesioner_alumni = "Sudah" THEN 1 ELSE 0 END) as total_sudah_isi'),
                    // Total responden atasan (hanya yang nama_atasan tidak null)
                    DB::raw('SUM(CASE WHEN project_responden.nama_atasan IS NOT NULL THEN 1 ELSE 0 END) as total_responden_atasan'),
                    // Total keterisian atasan (bandingkan dengan semua responden, tidak hanya yang nama_atasan tidak null)
                    DB::raw('SUM(CASE WHEN project_responden.status_pengisian_kuesioner_atasan = "Sudah" THEN 1 ELSE 0 END) as total_sudah_isi_atasan')
                )
                ->where('project.status', 'Pelaksanaan')
                ->groupBy('project.id', 'users.name', 'users.email', 'users.avatar')
                ->orderBy('project.id', 'desc')
                ->get();


            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('responden_alumni', function ($row) {
                    $showAlumni = route('penyebaran-kuesioner.responden-alumni.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                            <a href="' . $showAlumni . '"
                               class="btn btn-sm btn-warning"
                               style="width: 120px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                <i class="fas fa-users"></i> ' . $row->total_responden . ' Alumni
                            </a>
                        </div>';
                })

                ->addColumn('keterisian_alumni', function ($row) {
                    $total = $row->total_responden ?: 1; // Hindari pembagian dengan nol
                    $sudah = $row->total_sudah_isi;
                    $persentase = round(($sudah / $total) * 100, 2);

                    return "$sudah Alumni ($persentase%)";
                })

                // ->addColumn('config_alumni', function ($row) {
                //     $waAlumni = route('penyebaran-kuesioner.pesan.wa.show', ['id' => $row->id]);
                //     $kuesionerAlumni = route('penyebaran-kuesioner.kuesioner.show', ['id' => $row->id, 'remark' => 'Alumni']);
                //     $bobotKuesioner = route('penyebaran-kuesioner.bobot.show', ['id' => $row->id]);
                //     return '
                //         <div class="d-flex flex-column">
                //             <div class="d-flex gap-1 mb-1">
                //                 <a href="' . $waAlumni . '"
                //                    class="btn btn-sm btn-success"
                //                    data-toggle="tooltip" data-placement="left" title="Pesan WA Alumni">
                //                     <i class="fab fa-whatsapp"></i>
                //                 </a>
                //                 <a href="' . $kuesionerAlumni . '"
                //                    class="btn btn-sm btn-primary"
                //                    data-toggle="tooltip" data-placement="left" title="Kuesioner Alumni">
                //                     <i class="fa fa-file"></i>
                //                 </a>
                //             </div>

                //             <div class="d-flex gap-1 mb-1">
                //                 <a href="' . $bobotKuesioner . '"
                //                     class="btn btn-sm btn-danger"
                //                     data-toggle="tooltip" data-placement="left" title="Bobot Alumni">
                //                     <i class="fas fa-balance-scale"></i>
                //                  </a>
                //             </div>
                //         </div>';
                // })


                ->addColumn('responden_atasan', function ($row) {
                    $showAtasan = route('penyebaran-kuesioner.responden-atasan.show', ['id' => $row->id]);
                    return '
                        <div class="text-center">
                             <a href="' . $showAtasan . '"
                               class="btn btn-sm btn-warning"
                               style="width: 120px;"
                               data-toggle="tooltip" data-placement="left" title="Atur Bobot">
                                 <i class="fas fa-users"></i> ' . $row->total_responden_atasan . ' Atasan
                            </a>
                        </div>';
                })

                ->addColumn('keterisian_atasan', function ($row) {
                    $total = $row->total_responden ?: 1;
                    $sudah = $row->total_sudah_isi_atasan;
                    $persentase = round(($sudah / $total) * 100, 2);

                    return "$sudah Atasan ($persentase%)";
                })

                // ->addColumn('config_atasan', function ($row) {
                //     $waAtasan = route('penyebaran-kuesioner.pesan.wa.show', ['id' => $row->id]);
                //     $kuesionerAtasan = route('penyebaran-kuesioner.kuesioner.show', ['id' => $row->id, 'remark' => 'Atasan']);
                //     return '
                //         <div class="text-center d-flex flex-column align-items-center">
                //             <div class="d-flex gap-1 mb-1">
                //                 <a href="' . $waAtasan . '"
                //                    class="btn btn-sm btn-success"
                //                    data-toggle="tooltip" data-placement="left" title="Pesan WA Alumni">
                //                     <i class="fab fa-whatsapp"></i>
                //                 </a>
                //                 <a href="' . $kuesionerAtasan . '"
                //                    class="btn btn-sm btn-primary"
                //                    data-toggle="tooltip" data-placement="left" title="Kuesioner Alumni">
                //                     <i class="fa fa-file"></i>
                //                 </a>
                //             </div>
                //         </div>';
                // })


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
                ->addColumn('action', 'penyebaran-kuesioner.include.action')
                ->rawColumns(['action', 'responden_alumni', 'responden_atasan', 'keterisian_alumni', 'keterisian_atasan', 'config_alumni', 'config_atasan', 'user'])
                ->toJson();
        }

        return view('penyebaran-kuesioner.index');
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

        $aspeks = DB::table('aspek')
            ->select('id', 'aspek')
            ->where('diklat_type_id', $project->diklat_type_id)
            ->get();


        return view('penyebaran-kuesioner.kuesioner', compact('project', 'kuesioners', 'remark', 'aspeks'));
    }

    public function showRespondenAlumni($id): View|JsonResponse
    {
        if (request()->ajax()) {
            $respondens = DB::table('project_responden as pr')
                ->where('pr.project_id', $id)
                ->leftJoinSub(
                    DB::table('project_log_send_notif as log1')
                        ->select('log1.project_responden_id', 'log1.telepon', 'log1.status')
                        ->whereRaw('log1.created_at = (SELECT MAX(log2.created_at) FROM project_log_send_notif as log2 WHERE log2.project_responden_id = log1.project_responden_id AND log2.telepon = log1.telepon)')
                        ->orderByDesc('log1.created_at'),
                    'log_wa',
                    function ($join) {
                        $join->on('pr.id', '=', 'log_wa.project_responden_id')
                            ->on('pr.telepon', '=', 'log_wa.telepon');
                    }
                )
                ->select('pr.*', 'log_wa.status as wa_status')
                ->get();

            return DataTables::of($respondens)
                ->addIndexColumn()
                ->addColumn('telepon', function ($row) {
                    $telepon = $row->telepon ?? '-';
                    $badgeStyle = 'display: inline-block; width: 100px; text-align: center;';

                    $badge = '<span class="badge bg-warning" style="' . $badgeStyle . '">
                                <i class="fas fa-hourglass-half"></i> Menunggu
                              </span>';

                    if ($row->wa_status === 'Sukses') {
                        $badge = '<span class="badge bg-success" style="' . $badgeStyle . '">
                                    <i class="fas fa-check"></i> Sukses
                                  </span>';
                    } elseif ($row->wa_status === 'Gagal') {
                        $badge = '<span class="badge bg-danger" style="' . $badgeStyle . '">
                                    <i class="fas fa-times"></i> Gagal
                                  </span>';
                    }

                    return $telepon . '<br>' . $badge;
                })

                ->addColumn('action', 'penyebaran-kuesioner.include.action-responden-alumni')
                ->rawColumns(['telepon', 'action'])
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
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();

        return view('penyebaran-kuesioner.responden-alumni', compact('project', 'kriteriaResponden'));
    }

    public function showRespondenAtasan($id): View|JsonResponse
    {
        if (request()->ajax()) {
            $respondens = DB::table('project_responden as pr')
                ->where('pr.project_id', $id)
                ->whereNotNull('pr.nama_atasan')
                ->whereNotNull('pr.telepon_atasan')
                ->leftJoinSub(
                    DB::table('project_log_send_notif as log1')
                        ->select('log1.project_responden_id', 'log1.telepon', 'log1.status')
                        ->whereRaw('log1.created_at = (SELECT MAX(log2.created_at) FROM project_log_send_notif as log2 WHERE log2.project_responden_id = log1.project_responden_id AND log2.telepon = log1.telepon)')
                        ->orderByDesc('log1.created_at'),
                    'log_wa',
                    function ($join) {
                        $join->on('pr.id', '=', 'log_wa.project_responden_id')
                            ->on('pr.telepon_atasan', '=', 'log_wa.telepon');
                    }
                )
                ->select('pr.*', 'log_wa.status as wa_status')
                ->get();


            return DataTables::of($respondens)
                ->addIndexColumn()
                ->addColumn('telepon_atasan', function ($row) {
                    $telepon_atasan = $row->telepon_atasan ?? '-';
                    $badgeStyle = 'display: inline-block; width: 100px; text-align: center;';

                    $badge = '<span class="badge bg-warning" style="' . $badgeStyle . '">
                                <i class="fas fa-hourglass-half"></i> Menunggu
                              </span>';

                    if ($row->wa_status === 'Sukses') {
                        $badge = '<span class="badge bg-success" style="' . $badgeStyle . '">
                                    <i class="fas fa-check"></i> Sukses
                                  </span>';
                    } elseif ($row->wa_status === 'Gagal') {
                        $badge = '<span class="badge bg-danger" style="' . $badgeStyle . '">
                                    <i class="fas fa-times"></i> Gagal
                                  </span>';
                    }

                    return $telepon_atasan . '<br>' . $badge;
                })

                ->addColumn('action', 'penyebaran-kuesioner.include.action-responden-atasan')
                ->rawColumns(['telepon_atasan', 'action'])
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
            ->join('users', 'project.user_id', '=', 'users.id')
            ->select('project.*', 'users.name as user_name')
            ->where('project.id', $id)
            ->first();

        return view('penyebaran-kuesioner.responden-atasan', compact('project', 'kriteriaResponden'));
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
        return view('penyebaran-kuesioner.pesan_wa', compact('project', 'pesanWa'));
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

        return view('penyebaran-kuesioner.bobot', compact('project', 'level3', 'level4', 'dataSecondary'));
    }

    public function updateTelepon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:project_responden,id',
            'telepon' => 'required|string|min:10|max:15',
            'remark' => 'required|in:Alumni,Atasan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $fieldToUpdate = $request->remark === 'Alumni' ? 'telepon' : 'telepon_atasan';

            DB::table('project_responden')
                ->where('id', $request->id)
                ->update([$fieldToUpdate => $request->telepon]);

            return response()->json([
                'success' => true,
                'message' => 'Nomor telepon berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data!',
            ], 500);
        }
    }

    public function sendNotifWa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:project_responden,id',
            'remark' => 'required|in:Alumni,Atasan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $notifikasi = DB::table('project_responden')
                ->join('project_pesan_wa', 'project_responden.project_id', '=', 'project_pesan_wa.project_id')
                ->join('project', 'project_responden.project_id', '=', 'project.id')
                ->select(
                    'project_responden.*',
                    'project_pesan_wa.text_pesan_alumni',
                    'project.status',
                    'project.kaldikID',
                    'project.kaldikDesc'
                )
                ->where('project_responden.id', $request->id)->first();

            if (!$notifikasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data responden tidak ditemukan!',
                ], 404);
            }

            $telepon = $request->remark === 'Alumni' ? $notifikasi->telepon : $notifikasi->telepon_atasan;
            $try_send_wa = $request->remark === 'Alumni' ? $notifikasi->try_send_wa_alumni : $notifikasi->try_send_wa_atasan;
            $response = sendNotifWa($telepon, "Halo, jangan lupa mengisi kuesioner alumni!", $request->remark);
            $status = $response['status'];
            $statusText = $status ? 'Sukses' : 'Gagal';
            // Insert ke tabel log
            DB::table('project_log_send_notif')->insert([
                'telepon' => $telepon,
                'remark' => $request->remark,
                'status' => $statusText,
                'project_responden_id' => $notifikasi->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->updateStatus($notifikasi->id, $try_send_wa, $request->remark);
            if ($status) {
                $encryptedId = encryptShort($notifikasi->id);
                $encryptedTarget = encryptShort($request->remark);
                $url = URL::to(route('responden-kuesioner.index', ['id' => $encryptedId, 'target' => $encryptedTarget]));
            }

            $message = generateMessage($notifikasi, $status, $url ?? null, $response['message'] ?? null, $request->remark);

            if (!$status) {
                Log::error($message);
            }
            sendNotifTelegram($message, $request->remark);
            return response()->json([
                'success' => $status,
                'message' => $response['message'],
            ]);
        } catch (\Exception $e) {
            $this->updateStatus($notifikasi->id, $try_send_wa, $request->remark);
            // Insert ke tabel log jika terjadi error
            DB::table('project_log_send_notif')->insert([
                'telepon' => $telepon,
                'remark' => $request->remark,
                'status' => 'Gagal',
                'project_responden_id' => $notifikasi->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $errorMessage = generateMessage($notifikasi, false, null, $e->getMessage(), $request->remark);
            Log::error($errorMessage);
            sendNotifTelegram($errorMessage, 'atasan');
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLogNotifWa(Request $request)
    {
        $id = $request->id;
        $remark = $request->remark;
        $logs = DB::table('project_log_send_notif')
            ->where('project_responden_id', $id)
            ->where('remark', $remark)
            ->orderBy('created_at', 'desc');

        return DataTables::of($logs)
            ->make(true);
    }

    private function updateStatus($id, $trySendCount, $remark)
    {
        $updateData = [];

        if ($remark === 'Alumni') {
            $updateData = [
                'try_send_wa_alumni' => $trySendCount + 1,
                'last_send_alumni_at' => Carbon::now(),
            ];
        } elseif ($remark === 'Atasan') {
            $updateData = [
                'try_send_wa_atasan' => $trySendCount + 1,
                'last_send_atasan_at' => Carbon::now(),
            ];
        }

        if (!empty($updateData)) {
            DB::table('project_responden')
                ->where('id', $id)
                ->update($updateData);
        }
    }
}
