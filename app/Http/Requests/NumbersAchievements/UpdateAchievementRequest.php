<?php

namespace App\Http\Requests\NumbersAchievements;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAchievementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            "title_ar" => ["sometimes,nullable","string","max:255"],
            "title_en" => ["sometimes,nullable","string","max:255"],
            "number" => ["sometimes,required","numeric"],
            "number_suffix_ar" => ["sometimes,nullable","string","max:255"],
            "number_suffix_en" => ["sometimes,nullable","string","max:255"],
            "icon" => ["sometimes,nullable","string","max:100"],
            "image" => ["sometimes,nullable","string","max:2048"],
            "sort_order" => ["sometimes,nullable","integer","min:0"],
            "is_active" => ["sometimes,nullable","boolean"],
        ];
    }
}
