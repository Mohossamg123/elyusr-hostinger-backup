<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_category_id' => 'required|exists:service_categories,id',
            'slug' => 'required|string|max:255|unique:services,slug',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'badge_ar' => 'nullable|string|max:255',
            'badge_en' => 'nullable|string|max:255',
            'image_url' => 'nullable|url|max:2048',
            'short_intro_ar' => 'nullable|string',
            'short_intro_en' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'donation_options' => 'nullable|array',
            'details_bullets' => 'nullable|array',
            'seo_title_ar' => 'nullable|string|max:255',
            'seo_title_en' => 'nullable|string|max:255',
            'seo_description_ar' => 'nullable|string',
            'seo_description_en' => 'nullable|string',
        ];
    }
}
