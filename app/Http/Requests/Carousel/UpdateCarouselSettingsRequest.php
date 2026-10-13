<?php

namespace App\Http\Requests\Carousel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarouselSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['sometimes', 'boolean'],
            'autoplay' => ['sometimes', 'boolean'],
            'autoplay_speed' => ['sometimes', 'integer', 'min:500', 'max:60000'],
            'show_dots' => ['sometimes', 'boolean'],
        ];
    }
}
