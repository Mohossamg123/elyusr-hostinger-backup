<?php

namespace App\Http\Resources\NumbersAchievements;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeNumberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'number' => $this->number,
            'number_label_ar' => $this->number_label_ar,
            'number_label_en' => $this->number_label_en,
            'icon' => $this->icon,
            'fallback_image' => $this->fallback_image,
            'sort_order' => $this->sort_order,
            'is_visible' => $this->is_visible,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
