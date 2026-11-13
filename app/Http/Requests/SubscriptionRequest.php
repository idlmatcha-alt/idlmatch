<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
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
            'payment_method' => [
                'nullable',
                'string',
                Rule::in(['manual', 'bank_transfer', 'jazzcash', 'easypaisa', 'stripe', 'free']),
            ],
            'payment_proof' => [
                'required_if:payment_method,manual,bank_transfer,jazzcash,easypaisa',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }
}
