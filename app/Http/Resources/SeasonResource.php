<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,

            'period_ar' => $this->period_ar,
            'period_en' => $this->period_en,

            'tagline_ar' => $this->tagline_ar,
            'tagline_en' => $this->tagline_en,

            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,

            'is_active' => $this->is_active,
            'donation_available' => $this->donation_available,
            'sort_order' => $this->sort_order,

            'programs' => $this->when(
                $this->relationLoaded('programs'),
                fn () => $this->programs->map(fn ($program) => [
                    'id' => $program->id,
                    'name' => $program->name,
                    'name_ar' => $program->name_ar,
                    'name_en' => $program->name_en,
                    'slug' => $program->slug,
                    'status' => $program->status,
                    'is_active' => $program->is_active,
                ])->values()
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}