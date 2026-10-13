<?php

namespace App\Http\Requests\VolunteerCampaign;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVolunteerCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'governorate' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:500'],
            'required_volunteers' => ['required', 'integer', 'min:1'],
            'required_skills' => ['nullable', 'array'],
            'required_skills.*' => ['string', 'max:100'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'status' => [
                'nullable',
                Rule::in(['draft', 'open', 'closed', 'cancelled']),
            ],
        ];
    }
}
