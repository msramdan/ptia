<?php

namespace App\Http\Requests\Aspeks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAspekRequest extends FormRequest
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
            'level' => 'required|in:3,4',
			'aspek' => 'required|string|max:255',
            'kriteria' => 'required|in:Skor Persepsi,Delta Skor Persepsi',
			'urutan' => 'required|numeric',
        ];
    }
}
