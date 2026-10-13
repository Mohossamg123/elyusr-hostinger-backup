<?php

namespace App\Http\Controllers\Api\V1\Season;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSeasonRequest;
use App\Http\Requests\UpdateSeasonRequest;
use App\Http\Resources\SeasonResource;
use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index(Request $request)
    {
        $query = Season::with([
            'programs' => function ($query) {
                $query->latest();
            },
        ])->orderBy('sort_order')->latest();

        if ($request->has('is_active')) {
            $query->where('is_active', (bool) $request->is_active);
        }

        if ($request->has('donation_available')) {
            $query->where(
                'donation_available',
                (bool) $request->donation_available
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('tagline_ar', 'like', "%{$search}%")
                    ->orWhere('tagline_en', 'like', "%{$search}%");
            });
        }

        return SeasonResource::collection(
            $query->paginate(10)
        );
    }

    public function store(StoreSeasonRequest $request)
    {
        $data = $request->validated();

        $data['is_active'] = $data['is_active'] ?? true;
        $data['donation_available'] = $data['donation_available'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $season = Season::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Season created successfully',
            'data' => new SeasonResource($season),
        ], 201);
    }

    public function show(Season $season)
    {
        $season->load([
            'programs' => function ($query) {
                $query->latest();
            },
        ]);

        return new SeasonResource($season);
    }

    public function update(UpdateSeasonRequest $request, Season $season)
    {
        $season->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Season updated successfully',
            'data' => new SeasonResource(
                $season->fresh()->load('programs')
            ),
        ]);
    }

    public function destroy(Season $season)
    {
        $season->delete();

        return response()->json([
            'success' => true,
            'message' => 'Season deleted successfully',
        ]);
    }
}
