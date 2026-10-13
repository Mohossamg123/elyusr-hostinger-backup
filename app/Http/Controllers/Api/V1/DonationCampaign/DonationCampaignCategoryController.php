<?php

namespace App\Http\Controllers\Api\V1\DonationCampaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\DonationCampaign\StoreDonationCampaignCategoryRequest;
use App\Http\Requests\DonationCampaign\UpdateDonationCampaignCategoryRequest;
use App\Http\Resources\DonationCampaignCategoryResource;
use App\Models\DonationCampaignCategory;
use Illuminate\Http\Request;

class DonationCampaignCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = DonationCampaignCategory::query()
            ->when(
                $request->has('is_active'),
                fn ($query) =>
                    $query->where(
                        'is_active',
                        $request->boolean('is_active')
                    )
            )
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return DonationCampaignCategoryResource::collection(
            $categories
        );
    }

    public function store(
        StoreDonationCampaignCategoryRequest $request
    ) {
        $category = DonationCampaignCategory::create(
            $request->validated()
        );

        return response()->json([
            'message' => 'تم إنشاء تصنيف الحملات بنجاح',
            'data' => new DonationCampaignCategoryResource($category),
        ], 201);
    }

    public function show(
        DonationCampaignCategory $donationCampaignCategory
    ) {
        return new DonationCampaignCategoryResource(
            $donationCampaignCategory
        );
    }

    public function update(
        UpdateDonationCampaignCategoryRequest $request,
        DonationCampaignCategory $donationCampaignCategory
    ) {
        $donationCampaignCategory->update(
            $request->validated()
        );

        return response()->json([
            'message' => 'تم تحديث تصنيف الحملات بنجاح',
            'data' => new DonationCampaignCategoryResource(
                $donationCampaignCategory
            ),
        ]);
    }

    public function destroy(
        DonationCampaignCategory $donationCampaignCategory
    ) {
        $donationCampaignCategory->delete();

        return response()->json([
            'message' => 'تم حذف تصنيف الحملات بنجاح',
        ]);
    }
}
