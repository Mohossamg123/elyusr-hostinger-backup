<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'governorate',
        'city',
        'address',
        'skills',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
        ];
    }

    public function applications()
    {
        return $this->hasMany(VolunteerCampaignApplication::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(
            VolunteerCampaign::class,
            'volunteer_campaign_applications'
        )->withPivot([
            'status',
            'invited_at',
            'applied_at',
            'notes',
        ])->withTimestamps();
    }
}
