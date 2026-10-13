<?php

namespace App\Http\Controllers\Api\V1\Carousel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Carousel\StoreCarouselItemRequest;
use App\Http\Requests\Carousel\UpdateCarouselItemRequest;
use App\Http\Requests\Carousel\UpdateCarouselSettingsRequest;
use App\Http\Resources\Carousel\HomepageCarouselItemResource;
use App\Http\Resources\Carousel\HomepageCarouselSettingResource;
use App\Models\HomepageCarouselItem;
use App\Models\HomepageCarouselSetting;
use Illuminate\Http\Request;

class HomepageCarouselController extends Controller
{
    public function publicIndex()
    {
        $settings = HomepageCarouselSetting::first();

        if (!$settings || !$settings->is_active) {
            return response()->json([
                'data' => [
                    'settings' => $settings
                        ? new HomepageCarouselSettingResource($settings)
                        : null,
                    'items' => [],
                ],
            ]);
        }

        $items = HomepageCarouselItem::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => [
                'settings' => new HomepageCarouselSettingResource($settings),
                'items' => HomepageCarouselItemResource::collection($items),
            ],
        ]);
    }

    public function adminIndex()
    {
        $settings = HomepageCarouselSetting::first();

        $items = HomepageCarouselItem::orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => [
                'settings' => $settings
                    ? new HomepageCarouselSettingResource($settings)
                    : null,
                'items' => HomepageCarouselItemResource::collection($items),
            ],
        ]);
    }

    public function updateSettings(UpdateCarouselSettingsRequest $request)
    {
        $settings = HomepageCarouselSetting::first();

        if (!$settings) {
            $settings = HomepageCarouselSetting::create([
                'is_active' => true,
                'autoplay' => true,
                'autoplay_speed' => 2600,
                'show_dots' => true,
            ]);
        }

        $settings->update($request->validated());

        return new HomepageCarouselSettingResource(
            $settings->fresh()
        );
    }

    public function store(StoreCarouselItemRequest $request)
    {
        $item = HomepageCarouselItem::create(
            $request->validated()
        );

        return new HomepageCarouselItemResource($item);
    }

    public function show(HomepageCarouselItem $item)
    {
        return new HomepageCarouselItemResource($item);
    }

    public function update(
        UpdateCarouselItemRequest $request,
        HomepageCarouselItem $item
    ) {
        $item->update($request->validated());

        return new HomepageCarouselItemResource(
            $item->fresh()
        );
    }

    public function destroy(HomepageCarouselItem $item)
    {
        $item->delete();

        return response()->json([
            'message' => 'Carousel item deleted successfully',
        ]);
    }

    public function toggle(HomepageCarouselItem $item)
    {
        $item->update([
            'is_active' => !$item->is_active,
        ]);

        return new HomepageCarouselItemResource(
            $item->fresh()
        );
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:homepage_carousel_items,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($data['items'] as $item) {
            HomepageCarouselItem::where(
                'id',
                $item['id']
            )->update([
                'sort_order' => $item['sort_order'],
            ]);
        }

        return response()->json([
            'message' => 'Carousel order updated successfully',
        ]);
    }
}
