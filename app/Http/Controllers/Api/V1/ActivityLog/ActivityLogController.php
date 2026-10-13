<?php

namespace App\Http\Controllers\Api\V1\ActivityLog;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Get activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user.roles')
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('entity_type', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter by action
        |--------------------------------------------------------------------------
        */

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter by entity type
        |--------------------------------------------------------------------------
        */

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter by user
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = min(
            max((int) $request->get('per_page', 10), 1),
            100
        );

        $logs = $query->paginate($perPage);

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'per_page'     => $logs->perPage(),
                'total'        => $logs->total(),
            ],
        ]);
    }

    /**
     * Get activity log statistics.
     */
    public function statistics()
    {
        $totalActivities = ActivityLog::count();

        $adminActions = ActivityLog::whereIn('action', [
            'create',
            'update',
            'delete',
            'approve',
            'reject',
        ])->count();

        $contentUpdates = ActivityLog::where('action', 'update')
            ->whereIn('entity_type', [
                'Program',
                'Project',
                'CaseModel',
                'Service',
                'ServiceCategory',
                'Donation',
                'DonationCampaign',
                'BlogPost',
                'AboutUs',
            ])
            ->count();

        $activeSupervisors = ActivityLog::whereNotNull('user_id')
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('user_id')
            ->count('user_id');

        return response()->json([
            'data' => [
                'total_activities'    => $totalActivities,
                'admin_actions'      => $adminActions,
                'content_updates'    => $contentUpdates,
                'active_supervisors' => $activeSupervisors,
            ],
        ]);
    }
}