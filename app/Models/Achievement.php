<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        "title_ar",
        "title_en",
        "number",
        "number_suffix_ar",
        "number_suffix_en",
        "icon",
        "image",
        "sort_order",
        "is_active",
    ];

    protected $casts = [
        "number" => "decimal:2",
        "sort_order" => "integer",
        "is_active" => "boolean",
    ];
}
