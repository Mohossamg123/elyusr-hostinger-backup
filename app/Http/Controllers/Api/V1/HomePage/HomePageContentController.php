<?php

namespace App\Http\Controllers\Api\V1\HomePage;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomePage\UpdateHomePageContentRequest;
use App\Http\Resources\HomePage\HomePageContentResource;
use App\Models\HomePageContent;
use Illuminate\Support\Facades\Storage;

class HomePageContentController extends Controller
{
    private function getContent(): HomePageContent
    {
        return HomePageContent::firstOrCreate([]);
    }

    public function show()
    {
        return new HomePageContentResource($this->getContent());
    }

    /*
    |--------------------------------------------------------------------------
    | Hero
    |--------------------------------------------------------------------------
    */

    public function hero()
    {
        $content = $this->getContent();

        return response()->json([
            'success' => true,
            'data' => [

                // Hero Badge
                'hero_badge_ar' => $content->hero_badge_ar,
                'hero_badge_en' => $content->hero_badge_en,

                // Hero Title
                'hero_title_ar' => $content->hero_title_ar,
                'hero_title_en' => $content->hero_title_en,

                'hero_title_line1_ar' => $content->hero_title_line1_ar,
                'hero_title_line1_en' => $content->hero_title_line1_en,

                'hero_title_accent_ar' => $content->hero_title_accent_ar,
                'hero_title_accent_en' => $content->hero_title_accent_en,

                // Hero Description
                'hero_description_ar' => $content->hero_description_ar,
                'hero_description_en' => $content->hero_description_en,

                // Hero Image
                'hero_image' => $content->hero_image
                    ? Storage::disk('public')->url($content->hero_image)
                    : null,

                /*
                |--------------------------------------------------------------------------
                | Hero Primary Button
                |--------------------------------------------------------------------------
                */

                'primary_cta_ar' => $content->primary_cta_ar,
                'primary_cta_en' => $content->primary_cta_en,
                'primary_cta_url' => $content->primary_cta_url,
                'primary_cta_icon' => $content->primary_cta_icon,

                /*
                |--------------------------------------------------------------------------
                | Hero Secondary Button
                |--------------------------------------------------------------------------
                */

                'secondary_cta_ar' => $content->secondary_cta_ar,
                'secondary_cta_en' => $content->secondary_cta_en,
                'secondary_cta_url' => $content->secondary_cta_url,
                'secondary_cta_icon' => $content->secondary_cta_icon,

                // Transparency
                'transparency_ar' => $content->transparency_ar,
                'transparency_en' => $content->transparency_en,
            ],
        ]);
    }

    public function updateHero(UpdateHomePageContentRequest $request)
    {
        $content = $this->getContent();

        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Hero Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('hero_image')) {

            if ($content->hero_image) {
                Storage::disk('public')->delete($content->hero_image);
            }

            $data['hero_image'] = $request->file('hero_image')
                ->store('homepage', 'public');
        }

        $content->fill($data);
        $content->save();

        return $this->hero();
    }

    /*
    |--------------------------------------------------------------------------
    | Homepage Content / Section 2
    |--------------------------------------------------------------------------
    */

    public function content()
    {
        $content = $this->getContent();

        return response()->json([
            'success' => true,
            'data' => [

                // Badge
                'section2_badge_ar' => $content->section2_badge_ar,
                'section2_badge_en' => $content->section2_badge_en,

                // Title
                'section2_title_ar' => $content->section2_title_ar,
                'section2_title_en' => $content->section2_title_en,

                // Title Accent
                'section2_title_accent_ar' => $content->section2_title_accent_ar,
                'section2_title_accent_en' => $content->section2_title_accent_en,

                // Description
                'section2_description_ar' => $content->section2_description_ar,
                'section2_description_en' => $content->section2_description_en,

                /*
                |--------------------------------------------------------------------------
                | Section 2 Images
                |--------------------------------------------------------------------------
                */

                'section2_main_image' => $content->section2_main_image
                    ? Storage::disk('public')->url($content->section2_main_image)
                    : null,

                'section2_left_top_image' => $content->section2_left_top_image
                    ? Storage::disk('public')->url($content->section2_left_top_image)
                    : null,

                'section2_left_bottom_image' => $content->section2_left_bottom_image
                    ? Storage::disk('public')->url($content->section2_left_bottom_image)
                    : null,

                /*
                |--------------------------------------------------------------------------
                | Section 2 Card
                |--------------------------------------------------------------------------
                */

                'section2_card_title_ar' => $content->section2_card_title_ar,
                'section2_card_title_en' => $content->section2_card_title_en,

                'section2_card_subtitle_ar' => $content->section2_card_subtitle_ar,
                'section2_card_subtitle_en' => $content->section2_card_subtitle_en,

                'section2_card_url' => $content->section2_card_url,

                /*
                |--------------------------------------------------------------------------
                | Section 2 Primary Button
                |--------------------------------------------------------------------------
                */

                'section2_primary_cta_ar' => $content->section2_primary_cta_ar,
                'section2_primary_cta_en' => $content->section2_primary_cta_en,
                'section2_primary_cta_url' => $content->section2_primary_cta_url,
                'section2_primary_cta_icon' => $content->section2_primary_cta_icon,

                /*
                |--------------------------------------------------------------------------
                | Section 2 Secondary Button
                |--------------------------------------------------------------------------
                */

                'section2_secondary_cta_ar' => $content->section2_secondary_cta_ar,
                'section2_secondary_cta_en' => $content->section2_secondary_cta_en,
                'section2_secondary_cta_url' => $content->section2_secondary_cta_url,
                'section2_secondary_cta_icon' => $content->section2_secondary_cta_icon,
            ],
        ]);
    }

    public function updateContent(UpdateHomePageContentRequest $request)
    {
        $content = $this->getContent();

        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Section 2 Images
        |--------------------------------------------------------------------------
        */

        foreach ([
            'section2_main_image',
            'section2_left_top_image',
            'section2_left_bottom_image',
        ] as $field) {

            if ($request->hasFile($field)) {

                if ($content->{$field}) {
                    Storage::disk('public')->delete($content->{$field});
                }

                $data[$field] = $request->file($field)
                    ->store('homepage', 'public');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $content->fill($data);
        $content->save();

        return $this->content();
    }
}