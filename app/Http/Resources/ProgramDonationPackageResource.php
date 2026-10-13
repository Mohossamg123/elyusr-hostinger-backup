<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramDonationPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,

            'program_id' => $this->program_id,
            'project_id' => $this->project_id,
            'donation_campaign_id' => $this->donation_campaign_id,

            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,

            'amount' => $this->amount,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return array_filter(
            $data,
            static fn ($value) => $value !== null
        );
    }
}