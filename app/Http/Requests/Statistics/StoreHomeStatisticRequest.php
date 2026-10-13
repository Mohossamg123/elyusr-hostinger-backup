<?php

namespace App\Http\Requests\Statistics;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeStatisticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'title_ar' => 'required|string|max:255',

            'title_en' => 'nullable|string|max:255',

            'number' => 'required|string|max:255',

            'icon' => 'nullable|string|max:255',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'sort_order' => 'nullable|integer|min:0',

            'is_active' => 'nullable|boolean',
            
            'number_suffix_ar' => 'nullable|string|max:50',
             'number_suffix_en' => 'nullable|string|max:50',

        ];
    }
}
