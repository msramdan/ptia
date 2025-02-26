<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'nama_aplikasi' => 'required|string|max:255',
            'tentang_aplikasi' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'logo_login' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:2048',
            'pengumuman' => 'required|string',
            'is_aktif_pengumuman' => 'required|in:Yes,No',
            'jam_mulai' => 'sometimes|required|date_format:H:i',
            'jam_selesai' => 'sometimes|required|date_format:H:i|after:jam_mulai',
            'hari_libur' => 'nullable|array',
            'hari_libur.*' => 'in:0,1,2,3,4,5,6',
        ];
    }
}
