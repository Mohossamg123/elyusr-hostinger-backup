<?php

namespace App\Http\Requests\NumbersAchievements;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeNumberRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title_ar' => ['sometimes','nullable','string','max:255'],
            'title_en' => ['sometimes','nullable','string','max:255'],
            'number' => ['sometimes','numeric'],
            'number_label_ar' => ['sometimes','nullable','string','max:255'],
            'number_label_en' => ['sometimes','nullable','string','max:255'],
            'icon' => ['sometimes','nullable','string','max:100'],
            'fallback_image' => ['sometimes','nullable','string','max:2048'],
            'sort_order' => ['sometimes','integer','min:0'],
            'is_visible' => ['sometimes','boolean'],
        ];
    }
}
