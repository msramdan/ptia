<?php

namespace App\Http\Requests\Kuesioners;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKuesionerRequest extends FormRequest
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
			'pertanyaan' => 'required|string',
        ];
    }
}
