<?php

namespace App\Http\Requests\KriteriaRespondens;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKriteriaRespondenRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'nilai_post_test' => 'sometimes|array',
            'nilai_post_test.*' => 'in:Turun,Tetap,Naik',
            'nilai_post_test_minimal' => 'required|numeric',
        ];
    }
}
