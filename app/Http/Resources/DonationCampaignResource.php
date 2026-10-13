<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DonationCampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,

            'category_id' => $this->category_id,

            'category' => $this->when(
                $this->relationLoaded('category') && $this->category,
                fn () => [
                    'id' => $this->category->id,
                    'name_ar' => $this->category->name_ar,
                    'name_en' => $this->category->name_en,
                    'slug' => $this->category->slug,
                ]
            ),

            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug' => $this->slug,

            'subtitle_ar' => $this->subtitle_ar,
            'subtitle_en' => $this->subtitle_en,

            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,

            'image' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,

            'target_amount' => $this->target_amount,
            'collected_amount' => $this->collected_amount,
            'minimum_amount' => $this->minimum_amount,

            'donation_available' => $this->donation_available,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,

            /*
            |--------------------------------------------------------------------------
            | Benefits
            |--------------------------------------------------------------------------
            */

            'benefits' => $this->when(
                $this->relationLoaded('benefits'),
                fn () => $this->benefits->map(
                    fn ($benefit) => array_filter([
                        'id' => $benefit->id,
                        'title_ar' => $benefit->title_ar,
                        'title_en' => $benefit->title_en,
                        'sort_order' => $benefit->sort_order,
                        'is_active' => $benefit->is_active,
                    ], static fn ($value) => $value !== null)
                )->values()
            ),

            /*
            |--------------------------------------------------------------------------
            | Donation Packages
            |--------------------------------------------------------------------------
            */

            'donation_packages' => $this->when(
                $this->relationLoaded('donationPackages'),
                fn () => $this->donationPackages->map(
                    fn ($package) => array_filter([
                        'id' => $package->id,
                        'donation_campaign_id' => $package->donation_campaign_id,
                        'name_ar' => $package->name_ar,
                        'name_en' => $package->name_en,
                        'amount' => $package->amount,
                        'sort_order' => $package->sort_order,
                        'is_active' => $package->is_active,
                    ], static fn ($value) => $value !== null)
                )->values()
            ),

            /*
            |--------------------------------------------------------------------------
            | Cases
            |--------------------------------------------------------------------------
            */

            'cases' => $this->when(
                $this->relationLoaded('cases'),
                fn () => $this->cases->map(
                    fn ($case) => array_filter([
                        'id' => $case->id,
                        'donation_campaign_id' => $case->donation_campaign_id,
                        'category_id' => $case->category_id,
                        'code' => $case->code,
                        'title' => $case->title,
                        'beneficiary_name' => $case->beneficiary_name,
                        'target_amount' => $case->target_amount,
                        'deadline' => $case->deadline,
                        'priority' => $case->priority,
                        'governorate' => $case->governorate,
                        'city' => $case->city,
                        'cover_image' => $case->cover_image
                            ? Storage::disk('public')->url($case->cover_image)
                            : null,
                        'description' => $case->description,
                        'status' => $case->status,
                    ], static fn ($value) => $value !== null)
                )->values()
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