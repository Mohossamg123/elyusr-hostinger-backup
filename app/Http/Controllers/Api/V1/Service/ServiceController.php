<?php

namespace App\Http\Controllers\Api\V1\Service;

use App\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;

class ServiceController extends Controller
{
    public function index()
    {
        return ServiceResource::collection(
            Service::with('category')
                ->orderBy('sort_order')
                ->latest()
                ->paginate(10)
        );
    }

    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->validated());

        return new ServiceResource(
            $service->load('category')
        );
    }

    public function show(Service $service)
    {
        return new ServiceResource(
            $service->load('category')
        );
    }

    public function update(
        UpdateServiceRequest $request,
        Service $service
    ) {
        $service->update($request->validated());

        return new ServiceResource(
            $service->load('category')
        );
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully'
        ]);
    }
}
