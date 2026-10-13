<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerCampaignApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'volunteer_id' => $this->volunteer_id,
            'status' => $this->status,
            'invited_at' => $this->invited_at,
            'applied_at' => $this->applied_at,
            'notes' => $this->notes,
            'volunteer' => $this->whenLoaded(
                'volunteer',
                fn () => new VolunteerResource($this->volunteer)
            ),
            'campaign' => $this->whenLoaded(
                'campaign',
                fn () => new VolunteerCampaignResource($this->campaign)
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
