<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStepSixRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'father_name' => ['nullable', 'string', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'brothers_count' => ['required', 'integer', 'min:0', 'max:20'],
            'sisters_count' => ['required', 'integer', 'min:0', 'max:20'],
            'married_brothers_count' => ['nullable', 'integer', 'min:0', 'lte:brothers_count'],
            'married_sisters_count' => ['nullable', 'integer', 'min:0', 'lte:sisters_count'],
            'married_siblings_count' => ['required', 'integer', 'min:0'],
            'family_values' => ['nullable', 'string', 'max:50'],
            'family_type' => ['nullable', 'string', 'max:50'],
            'family_status' => ['nullable', 'string', 'max:50'],
        ];
    }
}
