<?php

namespace App\Http\Requests\indikatorPersepsi;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndikatorPersepsiRequest extends FormRequest
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
            'aspek_id' => 'required|exists:App\Models\Aspek,id',
			'indikator_persepsi' => 'required|in:1,2,3,4',
			'kriteria_persepsi' => 'required|in:Sangat tidak setuju,Tidak setuju,Setuju,Sangat setuju',
        ];
    }
}
