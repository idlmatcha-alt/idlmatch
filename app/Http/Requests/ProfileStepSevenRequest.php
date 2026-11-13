<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStepSevenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'city' => ['required', 'string', 'max:120'],
            'home_town' => ['nullable', 'string', 'max:120'],
            'home_location' => ['nullable', 'string', 'max:255'],
            'mobile_no' => ['required', 'string', 'max:40'],
        ];
    }
}
