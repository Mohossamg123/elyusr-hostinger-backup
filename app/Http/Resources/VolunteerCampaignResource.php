<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerCampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'event_date' => $this->event_date?->format('Y-m-d'),
            'event_time' => $this->event_time?->format('H:i'),
            'governorate' => $this->governorate,
            'city' => $this->city,
            'location' => $this->location,
            'required_volunteers' => $this->required_volunteers,
            'required_skills' => $this->required_skills ?? [],
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'status' => $this->status,
            'approved_volunteers_count' => $this->approved_volunteers_count,
            'remaining_volunteers' => $this->remaining_volunteers,
            'applications_count' => $this->applications()->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
