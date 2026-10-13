<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',
        'action',
        'method',
        'route_uri',
        'controller',
        'controller_method',
        'record_id',
        'payload',
        'route_parameters',
        'reason',
        'status',
        'reviewed_by',
        'review_note',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'route_parameters' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
