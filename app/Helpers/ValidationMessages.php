<?php

namespace App\Helpers;

class ValidationMessages
{
    public static function get()
    {
        return [
            'required' => ':attribute wajib diisi.',
            'exists' => ':attribute tidak ditemukan dalam sistem.',
            'integer' => ':attribute harus berupa angka.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'in' => ':attribute harus salah satu dari: :values.',
            'file' => ':attribute harus berupa file.',
            'mimes' => ':attribute harus berupa file dengan format: :values.',
            'uploaded' => ':attribute gagal diunggah.',
        ];
    }
}
