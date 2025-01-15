<?php

namespace App\Http\Requests\BobotAspeks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBobotAspekRequest extends FormRequest
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
			'bobot_alumni' => 'required|numeric',
			'bobot_atasan_langsung' => 'required|numeric',
        ];
    }
}
