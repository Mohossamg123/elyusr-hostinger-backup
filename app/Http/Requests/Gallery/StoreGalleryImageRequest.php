<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required_without:external_url|image|max:5120',
            'external_url' => 'nullable|url|max:2048',

            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',

            'alt_ar' => 'nullable|string|max:255',
            'alt_en' => 'nullable|string|max:255',

            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',

            'city' => 'nullable|string|max:255',
            'program' => 'nullable|string|max:255',
            'donation_program' => 'nullable|string|max:255',

            'sort_order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ];
    }
}
