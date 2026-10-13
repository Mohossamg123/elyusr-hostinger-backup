<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'slug',
        'title_ar',
        'title_en',
        'badge_ar',
        'badge_en',
        'image_url',
        'short_intro_ar',
        'short_intro_en',
        'sort_order',
        'is_active',
        'donation_options',
        'details_bullets',
        'seo_title_ar',
        'seo_title_en',
        'seo_description_ar',
        'seo_description_en',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'donation_options' => 'array',
            'details_bullets' => 'array',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}
