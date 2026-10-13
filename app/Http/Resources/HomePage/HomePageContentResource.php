<?php

namespace App\Http\Resources\HomePage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePageContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'hero' => [
                'badge_ar' => $this->hero_badge_ar,
                'badge_en' => $this->hero_badge_en,
                'title_ar' => $this->hero_title_ar,
                'title_en' => $this->hero_title_en,
                'title_line1_ar' => $this->hero_title_line1_ar,
                'title_line1_en' => $this->hero_title_line1_en,
                'title_accent_ar' => $this->hero_title_accent_ar,
                'title_accent_en' => $this->hero_title_accent_en,
                'description_ar' => $this->hero_description_ar,
                'description_en' => $this->hero_description_en,
                'image' => $this->hero_image,
                'primary_cta' => [
                    'ar' => $this->primary_cta_ar,
                    'en' => $this->primary_cta_en,
                    'url' => $this->primary_cta_url,
                ],
                'secondary_cta' => [
                    'ar' => $this->secondary_cta_ar,
                    'en' => $this->secondary_cta_en,
                    'url' => $this->secondary_cta_url,
                ],
                'transparency' => [
                    'ar' => $this->transparency_ar,
                    'en' => $this->transparency_en,
                ],
            ],

            'section2' => [
                'badge_ar' => $this->section2_badge_ar,
                'badge_en' => $this->section2_badge_en,
                'title_ar' => $this->section2_title_ar,
                'title_accent_ar' => $this->section2_title_accent_ar,
                'title_accent_en' => $this->section2_title_accent_en,
                'title_en' => $this->section2_title_en,
                'description_ar' => $this->section2_description_ar,
                'description_en' => $this->section2_description_en,
                'main_image' => $this->section2_main_image,
                'left_top_image' => $this->section2_left_top_image,
                'left_bottom_image' => $this->section2_left_bottom_image,
                'card' => [
                    'title_ar' => $this->section2_card_title_ar,
                    'title_en' => $this->section2_card_title_en,
                    'subtitle_ar' => $this->section2_card_subtitle_ar,
                    'subtitle_en' => $this->section2_card_subtitle_en,
                    'url' => $this->section2_card_url,
                ],
            ],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
