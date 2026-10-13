<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageContent extends Model
{
    protected $table = 'home_page_contents';

    protected $fillable = [
        'hero_badge_ar','hero_badge_en',
        'hero_title_ar','hero_title_en',
        'hero_title_line1_ar','hero_title_line1_en',
        'hero_title_accent_ar','hero_title_accent_en',
        'hero_description_ar','hero_description_en',
        'hero_image',
        'primary_cta_ar','primary_cta_en','primary_cta_url',
        'secondary_cta_ar','secondary_cta_en','secondary_cta_url',
        'primary_cta_icon',
        'secondary_cta_icon',
        'transparency_ar','transparency_en',
        'section2_badge_ar','section2_badge_en',
        'section2_title_ar','section2_title_en','section2_title_accent_ar','section2_title_accent_en',
        'section2_description_ar','section2_description_en',
        'section2_main_image','section2_left_top_image','section2_left_bottom_image',
        'section2_card_title_ar','section2_card_title_en',
        'section2_card_subtitle_ar','section2_card_subtitle_en',
        'section2_card_url',
        'section2_primary_cta_ar',
        'section2_primary_cta_en',
        'section2_primary_cta_url',
        'section2_primary_cta_icon',
        'section2_secondary_cta_ar',
        'section2_secondary_cta_en',
        'section2_secondary_cta_url',
        'section2_secondary_cta_icon',
    ];
}
