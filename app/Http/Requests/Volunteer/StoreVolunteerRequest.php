<?php

namespace App\Http\Requests\Volunteer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVolunteerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'governorate' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:100'],
            'status' => [
                'nullable',
                Rule::in(['pending', 'approved', 'rejected', 'inactive']),
            ],
            'notes' => ['nullable', 'string'],
        ];
    }
}
