<?php

namespace App\Http\Resources\NumbersAchievements;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title_ar" => $this->title_ar,
            "title_en" => $this->title_en,
            "number" => $this->number,
            "number_suffix_ar" => $this->number_suffix_ar,
            "number_suffix_en" => $this->number_suffix_en,
            "icon" => $this->icon,
            "image" => $this->image,
            "sort_order" => $this->sort_order,
            "is_active" => $this->is_active,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
