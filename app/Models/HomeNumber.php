<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'number',
        'number_label_ar',
        'number_label_en',
        'icon',
        'fallback_image',
        'sort_order',
        'is_visible',
        'created_by',
    ];

    protected $casts = [
        'number' => 'decimal:2',
        'sort_order' => 'integer',
        'is_visible' => 'boolean',
    ];
}
