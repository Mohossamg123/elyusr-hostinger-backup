<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $details = $this->details ?? [];

        if (is_array($details)) {
            foreach ([
                'secret_key',
                'api_key',
                'public_key',
                'private_key',
                'access_token',
                'client_secret',
                'token',
                'password',
            ] as $key) {
                unset($details[$key]);
            }
        }

        return [
            'id' => $this->id,

            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,

            'slug' => $this->slug,
            'type' => $this->type,

            'provider_ar' => $this->provider_ar,
            'provider_en' => $this->provider_en,

            'details' => $details,

            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,

            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}