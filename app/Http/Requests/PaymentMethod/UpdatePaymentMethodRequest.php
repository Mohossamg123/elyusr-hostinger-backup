<?php

namespace App\Http\Requests\PaymentMethod;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentMethodId = $this->route('paymentMethod')?->id;

        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],

            'slug' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('payment_methods', 'slug')->ignore($paymentMethodId),
            ],

            'type' => ['required', 'string', 'max:100'],

            'provider_ar' => ['nullable', 'string', 'max:255'],
            'provider_en' => ['nullable', 'string', 'max:255'],

            'details' => ['nullable', 'array'],

            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
