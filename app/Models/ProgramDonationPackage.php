<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramDonationPackage extends Model
{
    protected $fillable = [
        'program_id',
        'project_id',
        'donation_campaign_id',

        'name_ar',
        'name_en',
        'amount',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    // البرنامج
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    // المشروع
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // حملة التبرع
    public function donationCampaign(): BelongsTo
    {
        return $this->belongsTo(
            DonationCampaign::class,
            'donation_campaign_id'
        );
    }
}