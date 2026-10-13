<?php

namespace App\Http\Requests\Volunteer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVolunteerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'max:50'],
            'governorate' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'skills' => ['sometimes', 'nullable', 'array'],
            'skills.*' => ['string', 'max:100'],
            'status' => [
                'sometimes',
                Rule::in(['pending', 'approved', 'rejected', 'inactive']),
            ],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
