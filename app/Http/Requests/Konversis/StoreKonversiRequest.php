<?php

namespace App\Http\Requests\Konversis;

use Illuminate\Foundation\Http\FormRequest;

class StoreKonversiRequest extends FormRequest
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
            'diklat_type_id' => 'required',
            'jenis_skor' => 'required|string|max:255',
			'skor' => 'required|numeric',
			'konversi' => 'required|numeric',
        ];
    }
}
