<?php

namespace App\Http\Requests\DonationCampaign;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDonationCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('donation_campaigns', 'slug')
                    ->ignore($this->donationCampaign),
            ],

            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'target_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'minimum_amount' => [
                'nullable',
                'numeric',
                'min:1',
            ],

            'donation_available' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}