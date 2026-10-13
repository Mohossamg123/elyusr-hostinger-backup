<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'faq_category_id' => ['required', 'exists:faq_categories,id'],

            'question_ar' => ['required', 'string'],
            'question_en' => ['nullable', 'string'],

            'answer_ar' => ['required', 'string'],
            'answer_en' => ['nullable', 'string'],

            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}