<?php

namespace App\Http\Requests\KriteriaRespondens;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKriteriaRespondenRequest extends FormRequest
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
            'nilai_post_test' => 'required|in:Turun,Tetap,Naik',
			'nilai_pre_test_minimal' => 'required|numeric',
			'nilai_post_test_minimal' => 'required|numeric',
			'nilai_kenaikan_pre_post' => 'required|numeric',
        ];
    }
}
