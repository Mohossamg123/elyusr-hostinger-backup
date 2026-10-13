<?php

namespace App\Http\Resources\Newsletter;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsletterSubscriberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'status' => $this->status,
            'language' => $this->language,
            'source' => $this->source,
            'confirmed_at' => $this->confirmed_at,
            'unsubscribed_at' => $this->unsubscribed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
