<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStepFourRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'religion' => ['required', 'string', 'max:100'],
            'sect' => ['nullable', 'string', 'max:100'],
            'caste' => ['nullable', 'string', 'max:100'],
        ];
    }
}
