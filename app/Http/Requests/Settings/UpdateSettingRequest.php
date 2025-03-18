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
            'jam_mulai' => ['sometimes', 'required', 'date_format:H:i'],
            'jam_selesai' => ['sometimes', 'required', 'date_format:H:i', 'after:jam_mulai'],
            'hari_jalan_cron' => ['required', 'array', 'min:1'],
            'hari_jalan_cron.*' => ['integer', 'in:0,1,2,3,4,5,6'],
            'deadline_pengisian' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages()
    {
        return [
            'nama_aplikasi.required' => 'Nama aplikasi wajib diisi.',
            'nama_aplikasi.string' => 'Nama aplikasi harus berupa teks.',
            'nama_aplikasi.max' => 'Nama aplikasi tidak boleh lebih dari 255 karakter.',

            'tentang_aplikasi.required' => 'Tentang aplikasi wajib diisi.',
            'tentang_aplikasi.string' => 'Tentang aplikasi harus berupa teks.',

            'logo.image' => 'Logo harus berupa file gambar.',
            'logo.max' => 'Ukuran logo tidak boleh lebih dari 2MB.',

            'logo_login.image' => 'Logo login harus berupa file gambar.',
            'logo_login.max' => 'Ukuran logo login tidak boleh lebih dari 2MB.',

            'favicon.image' => 'Favicon harus berupa file gambar.',
            'favicon.max' => 'Ukuran favicon tidak boleh lebih dari 2MB.',

            'pengumuman.required' => 'Pengumuman wajib diisi.',
            'pengumuman.string' => 'Pengumuman harus berupa teks.',

            'is_aktif_pengumuman.required' => 'Status pengumuman wajib dipilih.',
            'is_aktif_pengumuman.in' => 'Status pengumuman harus "Yes" atau "No".',

            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM.',

            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai harus HH:MM.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',

            'hari_jalan_cron.required' => 'Pilih minimal satu hari untuk menjalankan cron.',
            'hari_jalan_cron.array' => 'Format hari jalan cron tidak valid.',
            'hari_jalan_cron.min' => 'Pilih minimal satu hari untuk menjalankan cron.',
            'hari_jalan_cron.*.integer' => 'Hari yang dipilih tidak valid.',
            'hari_jalan_cron.*.in' => 'Hari yang dipilih harus antara Minggu (0) hingga Sabtu (6).',

            'deadline_pengisian.required' => 'Deadline pengisian wajib diisi.',
            'deadline_pengisian.integer' => 'Deadline pengisian harus berupa angka.',
            'deadline_pengisian.min' => 'Deadline pengisian minimal adalah 1 hari.',
        ];
    }
}
