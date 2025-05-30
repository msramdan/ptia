<?php

namespace App\Http\Controllers;

use App\Models\DataSekunder;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ValidationMessages;

class DataSekunderController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:data sekunder view', only: ['index']),
            new Middleware('permission:data sekunder create', only: ['store']),
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
                ->join('diklat_type', 'project.diklat_type_id', '=', 'diklat_type.id')
                ->leftJoin('project_data_sekunder', 'project_data_sekunder.project_id', '=', 'project.id')
                ->select(
                    'project.id',
                    'project.kaldikID',
                    'project.kaldikDesc',
                    'project.created_at',
                    'project.tanggal_selesai',
                    'users.name as user_name',
                    'users.email',
                    'users.avatar',
                    'diklat_type.nama_diklat_type',
                    'project_data_sekunder.nilai_kinerja_awal',
                    'project_data_sekunder.nilai_kinerja_akhir',
                    'project_data_sekunder.berkas'
                )
                ->where('project.status', 'Pelaksanaan')
                ->when(request('evaluator'), function ($query, $evaluator) {
                    $query->where('project.user_id', $evaluator);
                })
                ->when(request('diklat_type'), function ($query, $diklatType) {
                    $query->where('project.diklat_type_id', $diklatType);
                })
                ->when(request('status_data_sekunder'), function ($query, $status) {
                    if ($status === 'Meningkat') {
                        $query->whereRaw('project_data_sekunder.nilai_kinerja_akhir > project_data_sekunder.nilai_kinerja_awal');
                    } elseif ($status === 'Tetap') {
                        $query->whereRaw('project_data_sekunder.nilai_kinerja_akhir = project_data_sekunder.nilai_kinerja_awal');
                    } elseif ($status === 'Menurun') {
                        $query->whereRaw('project_data_sekunder.nilai_kinerja_akhir < project_data_sekunder.nilai_kinerja_awal');
                    }
                })
                ->orderBy('project.id', 'desc');

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
                ->addColumn('berkas', function ($row) {
                    if ($row->berkas) {
                        $url = asset("storage/uploads/berkas/$row->berkas");
                        return '<a href="' . e($url) . '" target="_blank" class="btn btn-sm btn-success" title="Download Berkas">
                                <i class="fas fa-download"></i>
                            </a>';
                    }
                    return '-';
                })
                ->addColumn('data_sekunder', function ($row) {
                    if ($row->nilai_kinerja_awal !== null && $row->nilai_kinerja_akhir !== null) {
                        $status = $row->nilai_kinerja_akhir > $row->nilai_kinerja_awal
                            ? '<span class="badge bg-success"><i class="fas fa-arrow-up"></i> Meningkat</span>'
                            : ($row->nilai_kinerja_akhir < $row->nilai_kinerja_awal
                                ? '<span class="badge bg-danger"><i class="fas fa-arrow-down"></i> Menurun</span>'
                                : '<span class="badge bg-secondary"><i class="fas fa-minus"></i> Tetap</span>');

                        return '
                    <td class="text-center">
                        ' . e($row->nilai_kinerja_awal) . '-' . e($row->nilai_kinerja_akhir) . '
                        <hr style="margin:5px">
                        ' . $status . '
                    </td>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                <div class="text-center">
                    <button style="width: 140px" title="Input Data Sekunder" class="btn btn-sm btn-primary btn-action" data-id="' . $row->id . '">
                        <i class="fas fa-pencil"></i> Data Sekunder
                    </button>
                </div>';
                })
                ->rawColumns(['user', 'berkas', 'data_sekunder', 'action'])
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

        return view('data-sekunder.index', compact('evaluators', 'diklatTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:project,id',
            'nilai_kinerja_awal' => 'required|integer',
            'periode_awal' => 'required|string|max:150',
            'nilai_kinerja_akhir' => 'required|integer',
            'periode_akhir' => 'required|string|max:150',
            'satuan' => 'required|in:Persentase (%),Skor,Rupiah (Rp),Waktu (Jam),Waktu (Hari),Pcs,Unit,Item,Dollar (USD),Index',
            'sumber_data' => 'required|string|max:150',
            'unit_kerja' => 'required|string|max:255',
            'nama_pic' => 'required|string|max:150',
            'telpon' => 'required|string|max:15',
            'keterangan' => 'nullable|string',
            'berkas' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png,ppt,pptx|max:2048',
        ], ValidationMessages::get());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . json_encode($validator->errors()->all()));
        }

        try {
            $data = $request->except(['berkas']);
            $existingData = DataSekunder::where('project_id', $request->project_id)->first();

            // Handle file upload
            if ($request->hasFile('berkas')) {
                $file = $request->file('berkas');
                $timestamp = now()->format('YmdHis'); // Format waktu: 20240309123045
                $extension = $file->getClientOriginalExtension();
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = "{$timestamp}_{$originalName}.{$extension}";

                $file->storeAs('uploads/berkas', $filename, 'public');
                $data['berkas'] = $filename;

                // Hapus berkas lama jika ada
                if ($existingData && $existingData->berkas) {
                    Storage::disk('public')->delete('uploads/berkas/' . $existingData->berkas);
                }
            } else {
                // Gunakan berkas lama jika ada
                if ($existingData && $existingData->berkas) {
                    $data['berkas'] = $existingData->berkas;
                }
            }

            if ($existingData) {
                // Update data lama
                $existingData->update($data);
                $message = 'Data Sekunder berhasil diperbarui.';
            } else {
                // Simpan data baru
                DataSekunder::create($data);
                $message = 'Data Sekunder berhasil ditambahkan.';
            }

            return redirect()->route('data-sekunder.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getDataSekunder($project_id)
    {
        $data = DataSekunder::where('project_id', $project_id)->first();

        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
}
