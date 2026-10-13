<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,

            'category' => $this->category,

            'category_data' => $this->when(
                $this->relationLoaded('categoryRelation') && $this->categoryRelation,
                fn () => [
                    'id' => $this->categoryRelation->id,
                    'name_ar' => $this->categoryRelation->name_ar,
                    'name_en' => $this->categoryRelation->name_en,
                    'slug' => $this->categoryRelation->slug,
                ]
            ),

            'program' => $this->when(
                $this->relationLoaded('program') && $this->program,
                fn () => [
                    'id' => $this->program->id,
                    'name' => $this->program->name,
                    'name_ar' => $this->program->name_ar,
                    'name_en' => $this->program->name_en,
                ]
            ),

            'project' => $this->when(
                $this->relationLoaded('project') && $this->project,
                fn () => [
                    'id' => $this->project->id,
                    'name' => $this->project->name,
                ]
            ),

            'donation_campaign' => $this->when(
                $this->relationLoaded('donationCampaign')
                    && $this->donationCampaign,
                fn () => [
                    'id' => $this->donationCampaign->id,
                    'name_ar' => $this->donationCampaign->name_ar,
                    'name_en' => $this->donationCampaign->name_en,
                    'slug' => $this->donationCampaign->slug,
                ]
            ),

            'beneficiary_name' => $this->beneficiary_name,
            'age' => $this->age,
            'governorate' => $this->governorate,
            'city' => $this->city,
            'target_amount' => $this->target_amount,
            'deadline' => $this->deadline,
            'priority' => $this->priority,

            'cover_image' => $this->cover_image
                ? asset('storage/' . $this->cover_image)
                : null,

            'description' => $this->description,
            'status' => $this->status,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return array_filter(
            $data,
            static fn ($value) => $value !== null
        );
    }
}