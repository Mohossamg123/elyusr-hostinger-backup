<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public function log(
        string $action,
        ?Model $model = null,
        ?string $summary = null,
        array $metadata = []
    ): ActivityLog {
        $user = Auth::user();

        $role = null;

        if ($user) {
            $role = $user->roles()
                ->pluck('slug')
                ->first();
        }

        $entityType = $model
            ? class_basename($model)
            : null;

        $entityId = $model?->getKey();

        return ActivityLog::create([
            // Existing activity_logs columns
            'user_id'    => $user?->id,
            'module'     => $entityType ?? 'system',
            'action'     => $action,
            'description' => $summary,
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => null,
            'new_values' => !empty($metadata) ? $metadata : null,

            // New activity log columns
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'role'        => $role,
            'summary'     => $summary,
            'metadata'    => !empty($metadata) ? $metadata : null,
        ]);
    }

    public function created(
        Model $model,
        ?string $summary = null,
        array $metadata = []
    ): ActivityLog {
        return $this->log('create', $model, $summary, $metadata);
    }

    public function updated(
        Model $model,
        ?string $summary = null,
        array $metadata = []
    ): ActivityLog {
        return $this->log('update', $model, $summary, $metadata);
    }

    public function deleted(
        Model $model,
        ?string $summary = null,
        array $metadata = []
    ): ActivityLog {
        return $this->log('delete', $model, $summary, $metadata);
    }

    public function custom(
        string $action,
        ?Model $model = null,
        ?string $summary = null,
        array $metadata = []
    ): ActivityLog {
        return $this->log($action, $model, $summary, $metadata);
    }
}