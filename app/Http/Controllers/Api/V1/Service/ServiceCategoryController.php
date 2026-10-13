<?php

namespace App\Http\Controllers\Api\V1\Service;

use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCategoryResource;
use App\Http\Requests\Service\StoreServiceCategoryRequest;
use App\Http\Requests\Service\UpdateServiceCategoryRequest;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        return ServiceCategoryResource::collection(
            ServiceCategory::withCount('services')
                ->orderBy('sort_order')
                ->get()
        );
    }

    public function store(StoreServiceCategoryRequest $request)
    {
        $category = ServiceCategory::create($request->validated());

        return new ServiceCategoryResource($category);
    }

    public function show(ServiceCategory $service_category)
    {
        return new ServiceCategoryResource(
            $service_category->loadCount('services')
        );
    }

    public function update(
        UpdateServiceCategoryRequest $request,
        ServiceCategory $service_category
    ) {
        $service_category->update($request->validated());

        return new ServiceCategoryResource(
            $service_category->loadCount('services')
        );
    }

    public function destroy(ServiceCategory $service_category)
    {
        $service_category->delete();

        return response()->json([
            'message' => 'Service category deleted successfully'
        ]);
    }
}
