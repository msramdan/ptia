<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


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
                ->join('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
                ->leftJoin('project_responden', 'project_responden.project_id', '=', 'project.id')
                ->select(
                    'project.id',
                    'project.kaldikID',
                    'project.kaldikDesc',
                    'users.name as user_name',
                    'users.email',
                    'users.avatar',
                    'diklat_type.nama_diklat_type',
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
                ->orderBy('project.id', 'desc');


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

                    return "$sudah Alumni<br>($persentase%)";
                })

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
                    $total = $row->total_responden_atasan ?: 1;
                    $sudah = $row->total_sudah_isi_atasan;
                    $persentase = round(($sudah / $total) * 100, 2);

                    return "$sudah Atasan<br>($persentase%)";
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
                ->addColumn('action', 'penyebaran-kuesioner.include.action')
                ->rawColumns(['action', 'responden_alumni', 'responden_atasan', 'keterisian_alumni', 'keterisian_atasan', 'user'])
                ->toJson();
        }

        return view('penyebaran-kuesioner.index');
    }

    public function showRespondenAlumni($id): View|JsonResponse
    {
        if (request()->ajax()) {
            $respondens = DB::table('project_responden as pr')
                ->where('pr.project_id', $id)
                ->leftJoinSub(
                    DB::table('project_log_send_notif as log1')
                        ->select('log1.project_responden_id', 'log1.telepon', 'log1.status')
                        ->where('log1.remark', 'Alumni') // Tambahkan filter remark = 'Alumni'
                        ->whereRaw('log1.created_at = (
                        SELECT MAX(log2.created_at)
                        FROM project_log_send_notif as log2
                        WHERE log2.project_responden_id = log1.project_responden_id
                        AND log2.telepon = log1.telepon
                        AND log2.remark = "Alumni"
                    )')
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
                        ->where('log1.remark', 'Atasan')
                        ->whereRaw('log1.created_at = (SELECT MAX(log2.created_at) FROM project_log_send_notif as log2 WHERE log2.project_responden_id = log1.project_responden_id AND log2.telepon = log1.telepon AND log2.remark = "Atasan")')
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

    public function updateDeadline(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:project_responden,id',
            'deadline' => 'required|date_format:Y-m-d',
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
            $fieldToUpdate = $request->remark === 'Alumni' ? 'deadline_pengisian_alumni' : 'deadline_pengisian_atasan';

            DB::table('project_responden')
                ->where('id', $request->id)
                ->update([$fieldToUpdate => $request->deadline]);

            return response()->json([
                'success' => true,
                'message' => 'Deadline pengisian berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data!',
            ], 500);
        }
    }

    public function updateDeadlineSelected(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'deadline' => 'required|date',
                'remark' => 'required|string|in:Alumni,Atasan',
            ]);

            // Tentukan kolom yang akan diupdate berdasarkan remark
            $columnToUpdate = ($request->remark === 'Alumni')
                ? 'deadline_pengisian_alumni'
                : 'deadline_pengisian_atasan';

            // Update data di tabel project_responden
            DB::table('project_responden')
                ->whereIn('id', $request->ids)
                ->update([
                    $columnToUpdate => $request->deadline,
                ]);

            // Jika berhasil, kembalikan status true
            return response()->json([
                'status' => true,
                'message' => 'Deadline berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan status false
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
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
            $response = sendNotifWa($notifikasi, $telepon, $request->remark);
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

            if (env('SEND_NOTIF_TELEGRAM', false)) {
                sendNotifTelegram($message, $request->remark);
            }

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
            if (env('SEND_NOTIF_TELEGRAM', false)) {
                sendNotifTelegram($errorMessage, $request->remark);
            }

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

    /**
     * Export project details to PDF.
     *
     * @param  int  $id The Project ID
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportPenyebaranKuesionerPdf($id)
    {
        // try {
        // 1. Ambil data project utama
        $project = DB::table('project')
            ->join('users', 'project.user_id', '=', 'users.id')
            ->join('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
            ->select(
                'project.*',
                'users.name as user_name', // Nama pembuat
                'diklat_type.nama_diklat_type'
            )
            ->where('project.id', $id)
            ->first();

        if (!$project) {
            Log::warning('Gagal generate PDF: Project tidak ditemukan - ID ' . $id);
            return redirect()->route('project.index')->with('error', 'Project tidak ditemukan.');
        }

        // 2. Ambil Data Kuesioner
        $kuesionerAlumni = DB::table('project_kuesioner')
            ->join('aspek', 'project_kuesioner.aspek_id', '=', 'aspek.id')
            ->select('project_kuesioner.*', 'aspek.aspek as aspek_nama') // Pastikan alias aspek_nama digunakan
            ->where('project_kuesioner.project_id', $id)
            ->where('project_kuesioner.remark', 'Alumni')
            ->orderBy('project_kuesioner.id')
            ->get();

        $kuesionerAtasan = DB::table('project_kuesioner')
            ->join('aspek', 'project_kuesioner.aspek_id', '=', 'aspek.id')
            ->select('project_kuesioner.*', 'aspek.aspek as aspek_nama') // Pastikan alias aspek_nama digunakan
            ->where('project_kuesioner.project_id', $id)
            ->where('project_kuesioner.remark', 'Atasan')
            ->orderBy('project_kuesioner.id')
            ->get();

        // 3. Ambil Data Template Pesan WA
        $pesanWa = DB::table('project_pesan_wa')
            ->where('project_id', $id)
            ->first();
        $templateAlumni = $pesanWa ? $pesanWa->text_pesan_alumni : 'Template pesan alumni tidak ditemukan.';
        $templateAtasan = $pesanWa ? $pesanWa->text_pesan_atasan : 'Template pesan atasan tidak ditemukan.';

        // 4. Hitung Data Progress (Menggunakan logika dari contoh controller)
        $statusTarget = 'sudah'; // Status yang menandakan sudah mengisi (case-insensitive)

        // Progress Alumni
        $totalAlumni = DB::table('project_responden')
            ->where('project_id', $id)
            ->count();
        $mengisiAlumni = DB::table('project_responden')
            ->where('project_id', $id)
            ->whereRaw('LOWER(status_pengisian_kuesioner_alumni) = ?', [$statusTarget]) // Cek status alumni
            ->count();
        $persenAlumni = $totalAlumni > 0 ? round(($mengisiAlumni / $totalAlumni) * 100, 2) : 0;

        // Progress Atasan
        $totalAtasan = DB::table('project_responden')
            ->where('project_id', $id)
            ->whereNotNull('nama_atasan') // Filter responden yang punya data atasan
            ->count();
        $mengisiAtasan = DB::table('project_responden')
            ->where('project_id', $id)
            ->whereNotNull('nama_atasan') // Filter responden yang punya data atasan
            ->whereRaw('LOWER(status_pengisian_kuesioner_atasan) = ?', [$statusTarget]) // Cek status atasan
            ->count();
        $persenAtasan = $totalAtasan > 0 ? round(($mengisiAtasan / $totalAtasan) * 100, 2) : 0;


        // 5. Data Tambahan (logo, tanggal cetak)
        $namaPembuat = $project->user_name ?? 'N/A';
        $tanggalCetak = Carbon::now()->translatedFormat('d F Y H:i'); // Format tanggal Indonesia
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


        // 6. Siapkan data untuk dikirim ke view
        $data = [
            'project' => $project,
            'kuesionerAlumni' => $kuesionerAlumni,
            'kuesionerAtasan' => $kuesionerAtasan,
            'templateAlumni' => $templateAlumni,
            'templateAtasan' => $templateAtasan,
            'totalAlumni' => $totalAlumni,
            'mengisiAlumni' => $mengisiAlumni,
            'persenAlumni' => $persenAlumni,
            'totalAtasan' => $totalAtasan,
            'mengisiAtasan' => $mengisiAtasan,
            'persenAtasan' => $persenAtasan,
            'namaPembuat' => $namaPembuat,
            'tanggalCetak' => $tanggalCetak,
            'logoUrl' => $logoUrl
        ];

        // 7. Generate PDF
        // Nama view blade yang sudah dibuat: project.export-pdf
        $pdf = Pdf::loadView('penyebaran-kuesioner.export-pdf', $data);
        $pdf->setPaper('a4', 'portrait'); // Atur ukuran kertas dan orientasi jika perlu

        // 8. Atur nama file PDF yang akan diunduh
        $filename = 'Persiapan-Evaluasi-' . Str::slug($project->kaldikDesc ?? 'project') . '-' . $project->kaldikID . '.pdf';

        // 9. Tawarkan PDF untuk diunduh (atau tampilkan di browser dengan stream())
        // return $pdf->download($filename);
        return $pdf->stream($filename); // Gunakan stream untuk menampilkan di browser dulu


        // } catch (\Exception $e) {
        //     dd($e->getMessage());
        //     // Handle error jika terjadi kesalahan saat generate PDF
        //     Log::error('Error generating PDF for project ID ' . $id . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        //     return redirect()->route('penyebaran-kuesioner.index')->with('error', 'Gagal membuat PDF: Terjadi kesalahan internal. Silakan cek log.');
        // }
    }
}
