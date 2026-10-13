<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutUsPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'array'],
            'content.hero' => ['nullable', 'array'],
            'content.stats' => ['nullable', 'array'],
            'content.programs' => ['nullable', 'array'],
            'content.trust' => ['nullable', 'array'],
            'content.why_us' => ['nullable', 'array'],
            'content.values' => ['nullable', 'array'],
            'content.story' => ['nullable', 'array'],
            'content.vision_2030' => ['nullable', 'array'],
            'content.closing' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
