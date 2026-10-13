<?php

namespace App\Http\Requests\Newsletter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsletterSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('newsletter_subscribers', 'email')->ignore($this->route('newsletter_subscriber')),
            ],
            'status' => ['sometimes', Rule::in(['confirmed', 'pending', 'unsubscribed'])],
            'language' => ['sometimes', Rule::in(['ar', 'en'])],
            'source' => ['sometimes', 'nullable', 'string', 'max:100'],
        ];
    }
}
