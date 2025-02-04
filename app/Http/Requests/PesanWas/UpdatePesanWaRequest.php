<?php

namespace App\Http\Requests\PesanWas;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePesanWaRequest extends FormRequest
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
            'text_pesan_alumni' => 'required|string',
            'text_pesan_atasan' => 'required|string',
        ];
    }
}
