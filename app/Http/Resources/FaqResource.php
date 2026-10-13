<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'faq_category_id' => $this->faq_category_id,

            'question_ar' => $this->question_ar,
            'question_en' => $this->question_en,

            'answer_ar' => $this->answer_ar,
            'answer_en' => $this->answer_en,

            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,

            'category' => new FaqCategoryResource(
                $this->whenLoaded('category')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}