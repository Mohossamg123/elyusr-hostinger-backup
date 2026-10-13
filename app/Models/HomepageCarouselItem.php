<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageCarouselItem extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'image',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
