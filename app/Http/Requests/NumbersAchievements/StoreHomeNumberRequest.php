<?php

namespace App\Http\Requests\NumbersAchievements;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeNumberRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title_ar' => ['nullable','string','max:255'],
            'title_en' => ['nullable','string','max:255'],
            'number' => ['required','numeric'],
            'number_label_ar' => ['nullable','string','max:255'],
            'number_label_en' => ['nullable','string','max:255'],
            'icon' => ['nullable','string','max:100'],
            'fallback_image' => ['nullable','string','max:2048'],
            'sort_order' => ['nullable','integer','min:0'],
            'is_visible' => ['nullable','boolean'],
        ];
    }
}
