<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStepThreeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'job_type' => ['required', 'string', 'in:private,govt,business,unemployed'],
            'income' => ['nullable', 'string', 'max:255'],
            'nature_of_job' => ['nullable', 'string', 'max:255'],
            'profession' => ['required', 'string', 'max:150'],
        ];
    }
}
