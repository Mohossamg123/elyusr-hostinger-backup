<?php

namespace App\Http\Requests\HomePage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomePageContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Hero
            |--------------------------------------------------------------------------
            */

            'hero_badge_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'hero_badge_en' => ['sometimes', 'nullable', 'string', 'max:255'],

            'hero_title_ar' => ['sometimes', 'nullable', 'string'],
            'hero_title_en' => ['sometimes', 'nullable', 'string'],

            'hero_title_line1_ar' => ['sometimes', 'nullable', 'string'],
            'hero_title_line1_en' => ['sometimes', 'nullable', 'string'],

            'hero_title_accent_ar' => ['sometimes', 'nullable', 'string'],
            'hero_title_accent_en' => ['sometimes', 'nullable', 'string'],

            'hero_description_ar' => ['sometimes', 'nullable', 'string'],
            'hero_description_en' => ['sometimes', 'nullable', 'string'],

            'hero_image' => [
                'sometimes',
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            // Hero Primary Button
            'primary_cta_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'primary_cta_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'primary_cta_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'primary_cta_icon' => ['sometimes', 'nullable', 'string', 'max:100'],

            // Hero Secondary Button
            'secondary_cta_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'secondary_cta_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'secondary_cta_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'secondary_cta_icon' => ['sometimes', 'nullable', 'string', 'max:100'],

            'transparency_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'transparency_en' => ['sometimes', 'nullable', 'string', 'max:255'],


            /*
            |--------------------------------------------------------------------------
            | Section 2
            |--------------------------------------------------------------------------
            */

            'section2_badge_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section2_badge_en' => ['sometimes', 'nullable', 'string', 'max:255'],

            'section2_title_ar' => ['sometimes', 'nullable', 'string'],
            'section2_title_en' => ['sometimes', 'nullable', 'string'],

            'section2_title_accent_ar' => ['sometimes', 'nullable', 'string'],
            'section2_title_accent_en' => ['sometimes', 'nullable', 'string'],

            'section2_description_ar' => ['sometimes', 'nullable', 'string'],
            'section2_description_en' => ['sometimes', 'nullable', 'string'],


            /*
            |--------------------------------------------------------------------------
            | Section 2 Images
            |--------------------------------------------------------------------------
            */

            'section2_main_image' => [
                'sometimes',
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'section2_left_top_image' => [
                'sometimes',
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'section2_left_bottom_image' => [
                'sometimes',
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],


            /*
            |--------------------------------------------------------------------------
            | Section 2 Card
            |--------------------------------------------------------------------------
            */

            'section2_card_title_ar' => ['sometimes', 'nullable', 'string'],
            'section2_card_title_en' => ['sometimes', 'nullable', 'string'],

            'section2_card_subtitle_ar' => ['sometimes', 'nullable', 'string'],
            'section2_card_subtitle_en' => ['sometimes', 'nullable', 'string'],

            'section2_card_url' => ['sometimes', 'nullable', 'string', 'max:2048'],


            /*
            |--------------------------------------------------------------------------
            | Section 2 Primary Button
            |--------------------------------------------------------------------------
            */

            'section2_primary_cta_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section2_primary_cta_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section2_primary_cta_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'section2_primary_cta_icon' => ['sometimes', 'nullable', 'string', 'max:100'],


            /*
            |--------------------------------------------------------------------------
            | Section 2 Secondary Button
            |--------------------------------------------------------------------------
            */

            'section2_secondary_cta_ar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section2_secondary_cta_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section2_secondary_cta_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'section2_secondary_cta_icon' => ['sometimes', 'nullable', 'string', 'max:100'],
        ];
    }
}