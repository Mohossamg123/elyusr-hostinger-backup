<?php

namespace App\Http\Requests\NumbersAchievements;

use Illuminate\Foundation\Http\FormRequest;

class StoreAchievementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            "title_ar" => ["nullable","string","max:255"],
            "title_en" => ["nullable","string","max:255"],
            "number" => ["required","numeric"],
            "number_suffix_ar" => ["nullable","string","max:255"],
            "number_suffix_en" => ["nullable","string","max:255"],
            "icon" => ["nullable","string","max:100"],
            "image" => ["nullable","string","max:2048"],
            "sort_order" => ["nullable","integer","min:0"],
            "is_active" => ["nullable","boolean"],
        ];
    }
}
