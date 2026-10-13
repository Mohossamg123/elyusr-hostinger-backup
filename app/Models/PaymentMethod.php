<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'type',
        'provider_ar',
        'provider_en',
        'details',
        'description_ar',
        'description_en',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
