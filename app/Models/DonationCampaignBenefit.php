<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationCampaignBenefit extends Model
{
    protected $fillable = [
        'donation_campaign_id',
        'title_ar',
        'title_en',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(
            DonationCampaign::class,
            'donation_campaign_id'
        );
    }
}
