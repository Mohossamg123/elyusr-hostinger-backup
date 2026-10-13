<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageCarouselSetting extends Model
{
    protected $fillable = [
        'is_active',
        'autoplay',
        'autoplay_speed',
        'show_dots',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'autoplay' => 'boolean',
        'autoplay_speed' => 'integer',
        'show_dots' => 'boolean',
    ];
}
