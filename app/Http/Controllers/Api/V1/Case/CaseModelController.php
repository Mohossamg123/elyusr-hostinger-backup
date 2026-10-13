<?php

namespace App\Http\Controllers\Api\V1\Case;

use App\Http\Controllers\Controller;
use App\Http\Requests\Case\StoreCaseModelRequest;
use App\Http\Requests\Case\UpdateCaseModelRequest;
use App\Http\Resources\CaseResource;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CaseModelController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseModel::query()
            ->with([
                'categoryRelation',
                'program',
                'project',
                'donationCampaign',
            ]);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('donation_campaign_id')) {
            $query->where(
                'donation_campaign_id',
                $request->donation_campaign_id
            );
        }

        if ($request->filled('program_id')) {
            $query->where(
                'program_id',
                $request->program_id
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        if ($request->filled('priority')) {
            $query->where(
                'priority',
                $request->priority
            );
        }

        if ($request->filled('governorate')) {
            $query->where(
                'governorate',
                $request->governorate
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'title',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'beneficiary_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'city',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'governorate',
                    'like',
                    "%{$search}%"
                );
            });
        }

        $perPage = min(
            max(
                (int) $request->get('per_page', 10),
                1
            ),
            100
        );

        return CaseResource::collection(
            $query
                ->latest()
                ->paginate($perPage)
        );
    }

    public function statistics()
    {
        $counts = CaseModel::query()
            ->selectRaw(
                'status, COUNT(*) as total'
            )
            ->groupBy('status')
            ->pluck(
                'total',
                'status'
            );

        return response()->json([
            'total' => CaseModel::count(),

            'pending_review' => (int) (
                $counts['pending_review'] ?? 0
            ),

            'approved' => (int) (
                $counts['approved'] ?? 0
            ),

            'completed' => (int) (
                $counts['completed'] ?? 0
            ),

            'closed' => (int) (
                $counts['closed'] ?? 0
            ),

            'rejected' => (int) (
                $counts['rejected'] ?? 0
            ),
        ]);
    }

    public function store(StoreCaseModelRequest $request)
    {
        $data = $request->validated();

        $data['status'] = $data['status'] ?? 'pending_review';

        $category = \App\Models\CaseCategory::findOrFail(
            $data['category_id']
        );

        $data['category'] = $category->name_en
            ?: $category->name_ar;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request
                ->file('cover_image')
                ->store('cases', 'public');
        }

        $case = CaseModel::create($data);

        return response()->json([
            'message' => 'Case created successfully.',

            'data' => new CaseResource(
                $case->load([
                    'categoryRelation',
                    'program',
                    'project',
                    'donationCampaign',
                ])
            ),
        ], 201);
    }

    public function show(string $id)
    {
        return new CaseResource(
            CaseModel::with([
                'categoryRelation',
                'program',
                'project',
                'donationCampaign',
            ])->findOrFail($id)
        );
    }

    public function update(
        UpdateCaseModelRequest $request,
        string $id
    ) {
        $case = CaseModel::findOrFail($id);

        $data = $request->validated();

        if (isset($data['category_id'])) {
            $category = \App\Models\CaseCategory::findOrFail(
                $data['category_id']
            );

            $data['category'] = $category->name_en
                ?: $category->name_ar;
        }

        if ($request->hasFile('cover_image')) {
            if ($case->cover_image) {
                Storage::disk('public')->delete(
                    $case->cover_image
                );
            }

            $data['cover_image'] = $request
                ->file('cover_image')
                ->store('cases', 'public');
        }

        $case->update($data);

        return response()->json([
            'message' => 'Case updated successfully.',

            'data' => new CaseResource(
                $case->fresh()->load([
                    'categoryRelation',
                    'program',
                    'project',
                    'donationCampaign',
                ])
            ),
        ]);
    }

    public function destroy(string $id)
    {
        $case = CaseModel::findOrFail($id);

        if ($case->cover_image) {
            Storage::disk('public')->delete(
                $case->cover_image
            );
        }

        $case->delete();

        return response()->json([
            'message' => 'Case deleted successfully.',
        ]);
    }
}