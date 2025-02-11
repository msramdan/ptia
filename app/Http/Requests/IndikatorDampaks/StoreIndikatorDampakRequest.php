<?php

namespace App\Http\Requests\IndikatorDampaks;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndikatorDampakRequest extends FormRequest
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
            'nilai_minimal' => 'required|numeric',
			'nilai_maksimal' => 'required|numeric',
			'kriteria_dampak' => 'required|string',
        ];
    }
}
