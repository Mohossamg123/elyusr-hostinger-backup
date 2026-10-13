<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationCampaignCategory extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(
            DonationCampaign::class,
            'category_id'
        );
    }
}
