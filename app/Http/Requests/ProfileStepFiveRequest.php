<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStepFiveRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'home_status' => ['required', 'string', 'in:own,rented,sharing'],
            'home_size' => ['nullable', 'string', 'max:255'],
            'home_location' => ['nullable', 'string', 'max:255'],
            'other_properties' => ['nullable', 'string'],
        ];
    }
}
