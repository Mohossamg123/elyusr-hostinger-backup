<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewaySetting extends Model
{
    protected $fillable = [
        'gateway',
        'is_active',
        'credentials',
    ];

    protected $hidden = [
        'credentials',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credentials' => 'encrypted:array',
    ];
}