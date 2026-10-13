<?php

namespace App\Http\Middleware;

use App\Models\ContentChangeRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class EditorApprovalMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Super Admin and Admin-level users execute permitted changes immediately.
        // The route MUST already be protected by auth:sanctum + permission:<...>.
        if ($user->roles()->whereIn('slug', ['super-admin', 'admin'])->exists()) {
            return $next($request);
        }

        // Internal execution of an approved request must bypass creating another approval.
        if ($request->attributes->get('_approval_execution') === true) {
            return $next($request);
        }

        // Only Editors require approval.
        if (!$user->roles()->where('slug', 'editor')->exists()) {
            return $next($request);
        }

        $method = strtoupper($request->method());

        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        $route = $request->route();
        $uri = $request->path();
        $segments = $request->segments();

        // v1/<module>/...
        $module = $segments[1] ?? 'unknown';

        $action = match ($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => strtolower($method),
        };

        $recordId = null;

        foreach ($segments as $segment) {
            if (ctype_digit((string) $segment)) {
                $recordId = (int) $segment;
                break;
            }
        }

        $routeParameters = [];

        if ($route) {
            foreach ($route->parameters() as $key => $value) {
                if (is_object($value)) {
                    $routeParameters[$key] = [
                        'id' => isset($value->id) ? $value->id : null,
                        'class' => get_class($value),
                    ];
                } else {
                    $routeParameters[$key] = $value;
                }
            }
        }

        $controller = null;
        $controllerMethod = null;

        if ($route) {
            $actionName = $route->getActionName();

            if (is_string($actionName) && str_contains($actionName, '@')) {
                [$controller, $controllerMethod] = explode('@', $actionName, 2);
            }
        }

        // Never persist secrets/passwords/tokens in approval requests.
        $payload = $request->except(array_keys($request->allFiles()));
        $payload = $this->sanitizePayload($payload);

        if ($request->allFiles()) {
            $payload['_files'] = [];

            foreach ($request->allFiles() as $field => $files) {
                $files = is_array($files) ? $files : [$files];

                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $payload['_files'][] = [
                            'field' => $field,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ];
                    }
                }
            }
        }

        $approval = ContentChangeRequest::create([
            'user_id' => $user->id,
            'module' => $module,
            'action' => $action,
            'method' => $method,
            'route_uri' => $uri,
            'controller' => $controller,
            'controller_method' => $controllerMethod,
            'record_id' => $recordId,
            'payload' => $payload,
            'route_parameters' => $routeParameters,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Your change has been submitted for Admin approval.',
            'status' => 'pending',
            'approval_request_id' => $approval->id,
        ], 202);
    }

    private function sanitizePayload(array $payload): array
    {
        $sensitive = [
            'password',
            'password_confirmation',
            'current_password',
            'secret',
            'secret_key',
            'api_key',
            'apikey',
            'public_key',
            'private_key',
            'privatekey',
            'access_token',
            'refresh_token',
            'client_secret',
            'token',
            'authorization',
            'hmac_secret',
            'credentials',
        ];

        foreach ($payload as $key => &$value) {
            $normalized = strtolower(str_replace(['-', ' '], '_', (string) $key));

            if (in_array($normalized, $sensitive, true)) {
                $value = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $value = $this->sanitizePayload($value);
            }
        }

        return $payload;
    }
}
