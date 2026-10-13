<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'type' => $this->type,
            'status' => $this->status,
            'slug' => $this->slug,

            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,

            'summary_ar' => $this->summary_ar,
            'summary_en' => $this->summary_en,

            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,

            'cover_image' => $this->cover_image
                ? asset('storage/' . $this->cover_image)
                : null,

            'svg' => $this->svg,

            'tags' => $this->tags ?? [],

            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->author?->id,
                    'name' => $this->author?->name,
                    'email' => $this->author?->email,
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
