<?php

namespace App\Http\Requests\VolunteerCampaign;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVolunteerCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title_ar' => ['sometimes', 'required', 'string', 'max:255'],
            'title_en' => ['sometimes', 'required', 'string', 'max:255'],
            'event_date' => ['sometimes', 'required', 'date'],
            'event_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'governorate' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'location' => ['sometimes', 'nullable', 'string', 'max:500'],
            'required_volunteers' => ['sometimes', 'required', 'integer', 'min:1'],
            'required_skills' => ['sometimes', 'nullable', 'array'],
            'required_skills.*' => ['string', 'max:100'],
            'description_ar' => ['sometimes', 'nullable', 'string'],
            'description_en' => ['sometimes', 'nullable', 'string'],
            'status' => [
                'sometimes',
                Rule::in(['draft', 'open', 'closed', 'cancelled']),
            ],
        ];
    }
}
