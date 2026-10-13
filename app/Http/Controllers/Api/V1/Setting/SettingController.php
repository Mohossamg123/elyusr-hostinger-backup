<?php

namespace App\Http\Controllers\Api\V1\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $query = Setting::query();

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $settings = $query
            ->orderBy('group')
            ->orderBy('key')
            ->get();

        return SettingResource::collection($settings);
    }

    public function store(StoreSettingRequest $request)
    {
        $setting = Setting::create(
            $request->validated()
        );

        return (new SettingResource($setting))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Setting $setting)
    {
        return new SettingResource($setting);
    }

    public function update(
        UpdateSettingRequest $request,
        Setting $setting
    ) {
        $setting->update(
            $request->validated()
        );

        return new SettingResource(
            $setting->fresh()
        );
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return response()->json([
            'message' => 'Setting deleted successfully.',
        ]);
    }
}