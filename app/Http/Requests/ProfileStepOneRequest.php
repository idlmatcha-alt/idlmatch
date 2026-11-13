<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStepOneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'gender' => ['required', 'string', 'in:male,female'],
            'dob' => ['required', 'date', 'before:today'],
            'height_cm' => ['required', 'integer', 'between:120,220'],
            'disability' => ['nullable', 'string', 'max:255'],
            'mother_tongue' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['required', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'], // 10MB max
            'complexion' => ['nullable', 'string', 'max:50'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'eating_habits' => ['nullable', 'string', 'max:50'],
            'smoking_habits' => ['nullable', 'string', 'max:50'],
            'drinking_habits' => ['nullable', 'string', 'max:50'],
            'hobbies' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
