<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $key = strtolower((string) $this->key);

        $sensitivePatterns = [
            'password',
            'secret',
            'api_key',
            'apikey',
            'private_key',
            'privatekey',
            'access_token',
            'refresh_token',
            'client_secret',
            'hmac_secret',
            'authorization',
            'credential',
        ];

        $isSensitive = false;

        foreach ($sensitivePatterns as $pattern) {
            if (str_contains($key, $pattern)) {
                $isSensitive = true;
                break;
            }
        }

        $data = [
            'id' => $this->id,
            'key' => $this->key,

            'value' => $isSensitive
                ? '[REDACTED]'
                : $this->value,

            'type' => $this->type,
            'group' => $this->group,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return array_filter(
            $data,
            static fn ($value) => $value !== null
        );
    }
}