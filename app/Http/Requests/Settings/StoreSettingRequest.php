<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'nama_aplikasi' => 'required|string|max:255',
			'tentang_aplikasi' => 'required|string',
			'logo' => 'required|image|max:2048',
			'logo_login' => 'required|image|max:2048',
			'favicon' => 'required|image|max:2048',
			'pengumuman' => 'required|string',
			'is_aktif_pengumuman' => 'required|in:Yes,No',
        ];
    }
}
