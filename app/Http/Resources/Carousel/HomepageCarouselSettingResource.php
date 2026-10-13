<?php

namespace App\Http\Resources\Carousel;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomepageCarouselSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'autoplay' => $this->autoplay,
            'autoplay_speed' => $this->autoplay_speed,
            'show_dots' => $this->show_dots,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
