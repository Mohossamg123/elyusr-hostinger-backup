<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogSeo extends Model
{
    use HasFactory;

    protected $table = 'blog_seo';

    protected $fillable = [
        'content_id',
        'primary_keyword',
        'category_ar',
        'category_en',
        'excerpt_en',
        'tags_ar',
        'tags_en',
        'og_image',
        'og_image_url',
        'schema_type',
        'schema_data',
        'og_data',
        'seo_score',
        'word_count_ar',
        'word_count_en',
        'title_length_ar',
        'title_length_en',
        'description_length_ar',
        'description_length_en',
        'has_schema',
        'has_og',
        'has_category',
        'has_tags',
        'workflow_status',
        'scheduled_at',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tags_ar' => 'array',
        'tags_en' => 'array',
        'schema_data' => 'array',
        'og_data' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'seo_score' => 'integer',
        'word_count_ar' => 'integer',
        'word_count_en' => 'integer',
        'title_length_ar' => 'integer',
        'title_length_en' => 'integer',
        'description_length_ar' => 'integer',
        'description_length_en' => 'integer',
        'has_schema' => 'boolean',
        'has_og' => 'boolean',
        'has_category' => 'boolean',
        'has_tags' => 'boolean',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
