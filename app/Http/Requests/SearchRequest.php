<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'keyword' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'in:all,male,female'],
            'country' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:120'],
            'sect' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
            'caste' => ['nullable', 'string', 'max:100'],
            'mother_tongue' => ['nullable', 'string', 'max:100'],
            'display_id' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'education' => ['nullable', 'string', 'max:150'],
            'profession' => ['nullable', 'string', 'max:150'],
            'age_min' => ['nullable', 'integer', 'between:18,70'],
            'age_max' => ['nullable', 'integer', 'between:18,70'],
            'height_min' => ['nullable', 'integer', 'between:120,220'],
            'height_max' => ['nullable', 'integer', 'between:120,220'],
            'sort' => ['nullable', 'string', 'in:newest,recent'],
            'plan' => ['nullable', 'array'],
            'plan.*' => ['string'],
            'verified_only' => ['nullable', 'boolean'],
            'second_marriage' => ['nullable', 'boolean'],
            'divorced' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled('age_min') && $this->filled('age_max') && $this->integer('age_min') > $this->integer('age_max')) {
                $validator->errors()->add('age_max', 'The maximum age must be greater than or equal to the minimum age.');
            }

            if ($this->filled('height_min') && $this->filled('height_max') && $this->integer('height_min') > $this->integer('height_max')) {
                $validator->errors()->add('height_max', 'The maximum height must be greater than or equal to the minimum height.');
            }
        });
    }
}
