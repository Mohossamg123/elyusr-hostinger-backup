<?php

namespace App\Http\Resources\ContentApproval;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentChangeRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $payload = is_array($this->payload)
            ? $this->payload
            : [];

        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'secret',
            'secret_key',
            'api_key',
            'public_key',
            'private_key',
            'access_token',
            'refresh_token',
            'client_secret',
            'token',
            'authorization',
            'hmac_secret',
        ];

        foreach ($sensitiveKeys as $key) {
            if (array_key_exists($key, $payload)) {
                $payload[$key] = '[REDACTED]';
            }
        }

        if (isset($payload['credentials']) && is_array($payload['credentials'])) {
            foreach ($sensitiveKeys as $key) {
                if (array_key_exists($key, $payload['credentials'])) {
                    $payload['credentials'][$key] = '[REDACTED]';
                }
            }
        }

        $data = [
            'id' => $this->id,

            'user' => $this->whenLoaded('user', function () {
                return $this->user ? [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ] : null;
            }),

            'module' => $this->module,
            'action' => $this->action,
            'method' => $this->method,
            'route_uri' => $this->route_uri,
            'controller' => $this->controller,
            'controller_method' => $this->controller_method,
            'record_id' => $this->record_id,

            'payload' => $payload,

            'route_parameters' => $this->route_parameters,
            'reason' => $this->reason,
            'status' => $this->status,

            'reviewer' => $this->whenLoaded('reviewer', function () {
                return $this->reviewer ? [
                    'id' => $this->reviewer->id,
                    'name' => $this->reviewer->name,
                    'email' => $this->reviewer->email,
                ] : null;
            }),

            'review_note' => $this->review_note,
            'reviewed_at' => $this->reviewed_at?->toISOString(),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];

        return array_filter(
            $data,
            static fn ($value) => $value !== null
        );
    }
}