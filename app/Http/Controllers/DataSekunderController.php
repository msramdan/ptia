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
                ->select(
                    'project.*',
                    'users.name as user_name',
                    'users.email',
                    'users.avatar'
                )
                ->where('project.status', 'Pelaksanaan')
                ->orderBy('project.id', 'desc')
                ->get();

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
                ->addColumn('action', function ($row) {
                    return '
                    <div class="text-center">
                        <button class="btn btn-sm btn-primary btn-action" data-id="' . $row->id . '">
                            <i class="fas fa-plus"></i> Tambah Data Sekunder
                        </button>
                    </div>';
                })
                ->rawColumns(['user', 'action'])
                ->toJson();
        }

        return view('data-sekunder.index');
    }

    /**
     * Store a newly created resource in storage.
     */
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal, silakan periksa input Anda.');
        }

        try {
            $data = $request->all();

            if ($request->hasFile('berkas')) {
                $file = $request->file('berkas');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('uploads', $filename, 'public');
                $data['berkas'] = $filename;
            }

            DataSekunder::create($data);

            return redirect()->route('data-sekunder.index')
                ->with('success', 'Data Sekunder berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }
}
