<?php

namespace App\Http\Requests\ContentApproval;

use Illuminate\Foundation\Http\FormRequest;

class ReviewContentChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'review_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
