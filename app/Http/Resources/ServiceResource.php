<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => new ServiceCategoryResource($this->whenLoaded('category')),
            'service_category_id' => $this->service_category_id,
            'slug' => $this->slug,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'badge_ar' => $this->badge_ar,
            'badge_en' => $this->badge_en,
            'image_url' => $this->image_url,
            'short_intro_ar' => $this->short_intro_ar,
            'short_intro_en' => $this->short_intro_en,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'donation_options' => $this->donation_options,
            'details_bullets' => $this->details_bullets,
            'seo_title_ar' => $this->seo_title_ar,
            'seo_title_en' => $this->seo_title_en,
            'seo_description_ar' => $this->seo_description_ar,
            'seo_description_en' => $this->seo_description_en,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
