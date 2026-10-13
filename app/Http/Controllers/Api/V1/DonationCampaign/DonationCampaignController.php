<?php

namespace App\Http\Controllers\Api\V1\DonationCampaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\DonationCampaign\StoreDonationCampaignRequest;
use App\Http\Requests\DonationCampaign\UpdateDonationCampaignRequest;
use App\Http\Resources\DonationCampaignResource;
use App\Models\DonationCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationCampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = DonationCampaign::with([
            'category',
            'benefits',
            'donationPackages',
            'cases',
        ])
        ->when(
            $request->filled('category_id'),
            fn ($query) =>
                $query->where(
                    'category_id',
                    $request->category_id
                )
        )
        ->when(
            $request->has('is_active'),
            fn ($query) =>
                $query->where(
                    'is_active',
                    $request->boolean('is_active')
                )
        )
        ->when(
            $request->has('donation_available'),
            fn ($query) =>
                $query->where(
                    'donation_available',
                    $request->boolean('donation_available')
                )
        )
        ->orderBy('sort_order')
        ->orderByDesc('id')
        ->get();

        return DonationCampaignResource::collection($campaigns);
    }

    public function store(StoreDonationCampaignRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('donation-campaigns', 'public');
        }

        $campaign = DonationCampaign::create($data);

        return response()->json([
            'message' => 'تم إنشاء حملة التبرع بنجاح',
            'data' => new DonationCampaignResource(
                $campaign->load([
                    'category',
                    'benefits',
                    'donationPackages',
                    'cases',
                ])
            ),
        ], 201);
    }

    public function show(DonationCampaign $donationCampaign)
    {
        return new DonationCampaignResource(
            $donationCampaign->load([
                'category',
                'benefits',
                'donationPackages',
                'cases',
            ])
        );
    }

    public function update(
        UpdateDonationCampaignRequest $request,
        DonationCampaign $donationCampaign
    ) {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($donationCampaign->image) {
                Storage::disk('public')->delete(
                    $donationCampaign->image
                );
            }

            $data['image'] = $request
                ->file('image')
                ->store('donation-campaigns', 'public');
        }

        $donationCampaign->update($data);

        return response()->json([
            'message' => 'تم تحديث حملة التبرع بنجاح',
            'data' => new DonationCampaignResource(
                $donationCampaign->load([
                    'category',
                    'benefits',
                    'donationPackages',
                    'cases',
                ])
            ),
        ]);
    }

    public function destroy(DonationCampaign $donationCampaign)
    {
        if ($donationCampaign->image) {
            Storage::disk('public')->delete(
                $donationCampaign->image
            );
        }

        $donationCampaign->delete();

        return response()->json([
            'message' => 'تم حذف حملة التبرع بنجاح',
        ]);
    }
}