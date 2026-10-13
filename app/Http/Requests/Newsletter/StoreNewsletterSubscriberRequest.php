<?php

namespace App\Http\Requests\Newsletter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewsletterSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
            'status' => ['nullable', Rule::in(['confirmed', 'pending', 'unsubscribed'])],
            'language' => ['nullable', Rule::in(['ar', 'en'])],
            'source' => ['nullable', 'string', 'max:100'],
        ];
    }
}
