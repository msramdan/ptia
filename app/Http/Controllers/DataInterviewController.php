<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class DataInterviewController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:data interview view', only: ['index']),
            new Middleware('permission:data interview create', only: ['storeAlumniEvidence', 'storeAtasanEvidence']),
        ];
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $projects = DB::table('project as p')
                ->join('users as u', 'p.user_id', '=', 'u.id')
                ->join('diklat_type as dt', 'p.diklat_type_id', '=', 'dt.id')
                ->leftJoin('project_responden as pr', 'p.id', '=', 'pr.project_id')
                ->select(
                    'p.id',
                    'p.kaldikID',
                    'p.kaldikDesc',
                    'p.created_at',
                    'u.name as user_name',
                    'u.email',
                    'u.avatar',
                    'dt.nama_diklat_type',
                    DB::raw('COUNT(pr.id) as total_responden'),
                    DB::raw('COUNT(CASE WHEN pr.nama_atasan IS NULL THEN pr.id END) as alumni_count'),
                    DB::raw('COUNT(CASE WHEN pr.nama_atasan IS NOT NULL AND pr.status_pengisian_kuesioner_alumni = "Sudah" THEN pr.id END) as atasan_count')
                )
                ->where('p.status', 'Pelaksanaan')
                ->groupBy(
                    'p.id',
                    'p.kaldikID',
                    'p.kaldikDesc',
                    'u.name',
                    'u.email',
                    'u.avatar',
                    'dt.nama_diklat_type'
                )
                ->orderBy('p.id', 'desc');

            return DataTables::of($projects)
                ->addIndexColumn()
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
                ->addColumn('alumni', function ($row) {
                    $showAlumniUrl = route('data-interview.responden.alumni', ['project' => $row->id]);
                    $count = $row->total_responden ?? 0;
                    return '<div class="text-center">
                                <a href="' . $showAlumniUrl . '"
                                   class="btn btn-sm btn-warning" style="width: 120px;"
                                   title="Lihat Responden Alumni">
                                    <i class="fas fa-users"></i> ' . $count . ' Alumni
                                </a>
                            </div>';
                })
                ->addColumn('atasan', function ($row) {
                    $showAtasanUrl = route('data-interview.responden.atasan', ['project' => $row->id]);
                    $count = $row->atasan_count ?? 0;
                    return '<div class="text-center">
                                <a href="' . $showAtasanUrl . '"
                                   class="btn btn-sm btn-warning" style="width: 120px;"
                                   title="Lihat Responden Atasan">
                                    <i class="fas fa-user-tie"></i> ' . $count . ' Atasan
                                </a>
                            </div>';
                })
                ->rawColumns(['user', 'alumni', 'atasan'])
                ->toJson();
        }
        return view('data-interview.index');
    }

    public function showRespondenAlumni($projectId): View|JsonResponse
    {
        $project = DB::table('project as p')
            ->join('users as u', 'p.user_id', '=', 'u.id')
            ->select('p.id', 'p.kaldikID', 'p.kaldikDesc', 'u.name as user_name')
            ->where('p.id', $projectId)
            ->first();

        if (!$project) {
            abort(404, 'Project tidak ditemukan');
        }

        if (request()->ajax()) {
            $respondents = DB::table('project_responden')
                ->where('project_id', $projectId)
                ->select(
                    'id',
                    'nama',
                    'nip',
                    'jabatan',
                    'unit',
                    'hasil_intervie_alumni',
                    'evidence_intervie_alumni'
                );

            return DataTables::of($respondents)
                ->addIndexColumn()
                ->toJson();
        }

        return view('data-interview.responden-alumni', compact('project'));
    }

    public function showRespondenAtasan($projectId): View|JsonResponse
    {
        $project = DB::table('project as p')
            ->join('users as u', 'p.user_id', '=', 'u.id')
            ->select('p.id', 'p.kaldikID', 'p.kaldikDesc', 'u.name as user_name')
            ->where('p.id', $projectId)
            ->first();

        if (!$project) {
            abort(404, 'Project tidak ditemukan');
        }

        if (request()->ajax()) {
            $respondents = DB::table('project_responden')
                ->where('project_id', $projectId)
                ->whereNotNull('nama_atasan')
                ->whereNotNull('telepon_atasan')
                ->where('status_pengisian_kuesioner_alumni', 'Sudah')
                ->select(
                    'id',
                    'nama',
                    'nip',
                    'nama_atasan',
                    'telepon_atasan',
                    'hasil_intervie_atasan',
                    'evidence_intervie_atasan'
                );

            return DataTables::of($respondents)
                ->addIndexColumn()
                ->toJson();
        }

        return view('data-interview.responden-atasan', compact('project'));
    }

    public function storeAlumniEvidence(Request $request, $respondenId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'evidence_alumni_file' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'hasil_interview_alumni_text' => 'required|string',
        ], [
            'evidence_alumni_file.mimes' => 'Format file harus doc, docx, pdf, xls, xlsx, jpg, jpeg, atau png.',
            'evidence_alumni_file.max' => 'Ukuran file maksimal 5MB.',
            'hasil_interview_alumni_text.required' => 'Catatan hasil interview alumni harus diisi.', // pesan error untuk field required
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $responden = DB::table('project_responden')->find($respondenId);
        if (!$responden) {
            return response()->json(['success' => false, 'message' => 'Responden tidak ditemukan.'], 404);
        }


        try {
            $filename = $responden->evidence_intervie_alumni;

            if ($request->hasFile('evidence_alumni_file')) {
                $file = $request->file('evidence_alumni_file');
                if (!$file->isValid()) {
                    return response()->json(['success' => false, 'message' => 'File upload gagal.'], 400);
                }

                $newFilename = $responden->project_id . '_' . $respondenId . '_alumni_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = storage_path('app/public/uploads/data-interview-alumni');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $newFilename);

                if ($filename && $filename !== $newFilename) {
                    $oldPath = $destinationPath . '/' . $filename;
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $filename = $newFilename;
            }

            DB::table('project_responden')
                ->where('id', $respondenId)
                ->update([
                    'evidence_intervie_alumni' => $filename,
                    'hasil_intervie_alumni' => $request->input('hasil_interview_alumni_text'),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Data interview Alumni berhasil disimpan.',
                'new_filename' => $filename,
                'new_text' => $request->input('hasil_interview_alumni_text'),
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal simpan data interview alumni untuk responden {$respondenId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    public function storeAtasanEvidence(Request $request, $respondenId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'evidence_atasan_file' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'hasil_interview_atasan_text' => 'required|string',
        ], [
            'evidence_atasan_file.mimes' => 'Format file harus doc, docx, pdf, xls, xlsx, jpg, jpeg, atau png.',
            'evidence_atasan_file.max' => 'Ukuran file maksimal 5MB.',
            'hasil_interview_atasan_text.required' => 'Catatan hasil interview atasan harus diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $responden = DB::table('project_responden')->find($respondenId);
        if (!$responden || !$responden->nama_atasan) {
            return response()->json(['success' => false, 'message' => 'Responden atasan tidak valid.'], 404);
        }

        try {
            $filename = $responden->evidence_intervie_atasan;

            if ($request->hasFile('evidence_atasan_file')) {
                $file = $request->file('evidence_atasan_file');
                if (!$file->isValid()) {
                    return response()->json(['success' => false, 'message' => 'File upload gagal.'], 400);
                }

                $newFilename = $responden->project_id . '_' . $respondenId . '_atasan_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = storage_path('app/public/uploads/data-interview-atasan/');

                // Buat folder jika belum ada
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $newFilename);

                // Hapus file lama jika berbeda
                if ($filename && $filename !== $newFilename && file_exists($destinationPath . $filename)) {
                    unlink($destinationPath . $filename);
                }

                $filename = $newFilename;
            }

            DB::table('project_responden')
                ->where('id', $respondenId)
                ->update([
                    'evidence_intervie_atasan' => $filename,
                    'hasil_intervie_atasan' => $request->input('hasil_interview_atasan_text'),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Data interview Atasan berhasil disimpan.',
                'new_filename' => $filename,
                'new_text' => $request->input('hasil_interview_atasan_text'),
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal simpan data interview atasan untuk responden {$respondenId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }
}
