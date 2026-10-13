<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerCampaignApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'volunteer_id',
        'status',
        'invited_at',
        'applied_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'invited_at' => 'datetime',
            'applied_at' => 'datetime',
        ];
    }

    public function campaign()
    {
        return $this->belongsTo(
            VolunteerCampaign::class,
            'campaign_id'
        );
    }

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
}
