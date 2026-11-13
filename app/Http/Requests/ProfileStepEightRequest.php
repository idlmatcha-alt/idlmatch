<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStepEightRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'desired_height_min' => ['nullable', 'integer', 'between:120,220'],
            'desired_height_max' => ['nullable', 'integer', 'between:120,220'],
            'desired_age_min' => ['nullable', 'integer', 'between:18,99'],
            'desired_age_max' => ['nullable', 'integer', 'between:18,99'],
            'desired_caste' => ['nullable', 'string', 'max:100'],
            'desired_education' => ['nullable', 'string', 'max:255'],
            'desired_marital_status' => ['nullable', 'string', 'max:100'],
            'desired_religion' => ['nullable', 'string', 'max:100'],
            'desired_sect' => ['nullable', 'string', 'max:100'],
            'desired_country' => ['nullable', 'string', 'max:120'],
            'desired_state' => ['nullable', 'string', 'max:120'],
            'desired_city' => ['nullable', 'string', 'max:120'],
            'desired_mother_tongue' => ['nullable', 'string', 'max:120'],
            'desired_profession' => ['nullable', 'string', 'max:255'],
            'desired_religiousness' => ['nullable', 'string', 'max:100'],
            'desired_willing_to_relocate' => ['nullable', 'string', 'max:100'],
            'desired_living_arrangement' => ['nullable', 'string', 'max:100'],
            'desired_income' => ['nullable', 'string', 'max:100'],
            'desired_disability' => ['nullable', 'string', 'max:120'],
            'desired_complexion' => ['nullable', 'string', 'max:50'],
            'desired_body_type' => ['nullable', 'string', 'max:50'],
            'desired_family_values' => ['nullable', 'string', 'max:50'],
            'desired_eating_habits' => ['nullable', 'string', 'max:50'],
            'desired_smoking_habits' => ['nullable', 'string', 'max:50'],
            'other_requirements' => ['nullable', 'string'],
        ];
    }
}
