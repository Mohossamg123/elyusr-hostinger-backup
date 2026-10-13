<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,

            'axis_id' => $this->axis_id,

            'axis' => $this->when(
                $this->relationLoaded('axis') && $this->axis,
                fn () => [
                    'id' => $this->axis->id,
                    'slug' => $this->axis->slug,
                    'name_ar' => $this->axis->name_ar,
                    'name_en' => $this->axis->name_en,
                ]
            ),

            'seasons' => $this->when(
                $this->relationLoaded('seasons'),
                fn () => $this->seasons->map(fn ($season) => array_filter([
                    'id' => $season->id,
                    'name_ar' => $season->name_ar,
                    'name_en' => $season->name_en,
                    'period_ar' => $season->period_ar,
                    'period_en' => $season->period_en,
                    'tagline_ar' => $season->tagline_ar,
                    'tagline_en' => $season->tagline_en,
                    'is_active' => $season->is_active,
                    'donation_available' => $season->donation_available,
                ], static fn ($value) => $value !== null))->values()
            ),

            'slug' => $this->slug,

            'name' => $this->name,
            'name_ar' => $this->name_ar ?? $this->name,
            'name_en' => $this->name_en,

            'title_ar' => $this->name_ar ?? $this->name,
            'title_en' => $this->name_en,

            'period_ar' => $this->period_ar,
            'period_en' => $this->period_en,

            'tagline_ar' => $this->tagline_ar,
            'tagline_en' => $this->tagline_en,

            'donation_available' => $this->donation_available,

            'description' => $this->description,
            'description_ar' => $this->description_ar ?? $this->description,
            'description_en' => $this->description_en,

            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,

            'benefits_ar' => $this->benefits_ar ?? [],
            'benefits_en' => $this->benefits_en ?? [],

            'image' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,

            'target_amount' => $this->target_amount,
            'minimum_amount' => $this->minimum_amount,

            'status' => $this->status,
            'sort_order' => $this->sort_order,

            'meta_title_ar' => $this->meta_title_ar,
            'meta_title_en' => $this->meta_title_en,
            'meta_description_ar' => $this->meta_description_ar,
            'meta_description_en' => $this->meta_description_en,
            'keywords_ar' => $this->keywords_ar,
            'keywords_en' => $this->keywords_en,
            'canonical_url' => $this->canonical_url,
            'schema_type' => $this->schema_type,
            'twitter_card' => $this->twitter_card,
            'noindex' => $this->noindex,
            'nofollow' => $this->nofollow,

            'geo_region' => $this->geo_region,
            'geo_placename' => $this->geo_placename,
            'geo_position' => $this->geo_position,
            'geo_position_label' => $this->geo_position_label,

            'ai_summary_ar' => $this->ai_summary_ar,
            'ai_summary_en' => $this->ai_summary_en,
            'ai_faq_ar' => $this->ai_faq_ar,
            'ai_faq_en' => $this->ai_faq_en,

            'is_active' => $this->is_active,
            'hide_from_audience' => $this->hide_from_audience,

            /*
            |--------------------------------------------------------------------------
            | Donation Packages
            |--------------------------------------------------------------------------
            */

            'donation_packages' => $this->when(
                $this->relationLoaded('donationPackages'),
                fn () => $this->donationPackages->map(fn ($package) => [
                    'id' => $package->id,
                    'program_id' => $package->program_id,
                    'name_ar' => $package->name_ar,
                    'name_en' => $package->name_en,
                    'amount' => $package->amount,
                    'sort_order' => $package->sort_order,
                    'is_active' => $package->is_active,
                ])->values()
            ),

            /*
            |--------------------------------------------------------------------------
            | Cases
            |--------------------------------------------------------------------------
            */

            'cases' => $this->when(
                $this->relationLoaded('cases'),
                fn () => $this->cases->map(fn ($case) => array_filter([
                    'id' => $case->id,
                    'program_id' => $case->program_id,
                    'category_id' => $case->category_id,
                    'code' => $case->code,
                    'title' => $case->title,
                    'beneficiary_name' => $case->beneficiary_name,
                    'target_amount' => $case->target_amount,
                    'minimum_amount' => $case->minimum_amount,
                    'status' => $case->status,
                    'priority' => $case->priority,
                    'governorate' => $case->governorate,
                    'city' => $case->city,
                    'cover_image' => $case->cover_image
                        ? Storage::disk('public')->url($case->cover_image)
                        : null,
                ], static fn ($value) => $value !== null))->values()
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return array_filter(
            $data,
            static fn ($value) => $value !== null
        );
    }
}