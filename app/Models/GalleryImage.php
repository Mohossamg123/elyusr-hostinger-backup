<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'external_url',
        'title_ar',
        'title_en',
        'alt_ar',
        'alt_en',
        'description_ar',
        'description_en',
        'city',
        'program',
        'donation_program',
        'sort_order',
        'is_featured',
        'is_public',
        'created_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
