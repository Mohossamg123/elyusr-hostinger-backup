<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,
            'external_url' => $this->external_url,

            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,

            'alt_ar' => $this->alt_ar,
            'alt_en' => $this->alt_en,

            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,

            'city' => $this->city,
            'program' => $this->program,
            'donation_program' => $this->donation_program,

            'sort_order' => $this->sort_order,
            'is_featured' => $this->is_featured,
            'is_public' => $this->is_public,

            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
