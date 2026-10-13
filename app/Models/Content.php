<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'slug',
        'title_ar',
        'title_en',
        'summary_ar',
        'summary_en',
        'content_ar',
        'content_en',
        'cover_image',
        'svg',
        'tags',
        'author_id',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function blogSeo()
    {
        return $this->hasOne(BlogSeo::class, 'content_id');
    }
}
