<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'target_amount',
        'collected_amount',
        'is_active',
    ];

    public function donationPackages(): HasMany
    {
        return $this->hasMany(
            ProgramDonationPackage::class,
            'project_id'
        );
    }

    public function cases(): HasMany
    {
        return $this->hasMany(
            CaseModel::class,
            'project_id'
        );
    }
}