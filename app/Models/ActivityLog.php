<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',

        // Existing columns
        'module',
        'action',
        'description',
        'ip',
        'user_agent',
        'old_values',
        'new_values',

        // New columns
        'entity_type',
        'entity_id',
        'role',
        'summary',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata'   => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}