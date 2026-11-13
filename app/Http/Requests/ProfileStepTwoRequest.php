<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStepTwoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'education' => ['required', 'string', 'max:150'],
            'college' => ['nullable', 'string', 'max:255'],
            'university' => ['nullable', 'string', 'max:255'],
            'profession' => ['required', 'string', 'max:100'],
            'job_type' => ['nullable', 'string', 'max:100'],
            'income' => ['nullable', 'string', 'max:50'],
        ];
    }
}
