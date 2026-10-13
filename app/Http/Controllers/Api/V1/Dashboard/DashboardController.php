<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Donor;
use App\Models\Donation;
use App\Models\Project;
use App\Models\Program;
use App\Models\Volunteer;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'statistics' => [
                'users' => User::count(),
                'donors' => Donor::count(),
                'donations' => Donation::count(),
                'projects' => Project::count(),
                'programs' => Program::count(),

                // Volunteers
                'volunteers' => Volunteer::count(),
                'pending_volunteers' => Volunteer::where('status', 'pending')->count(),
                'approved_volunteers' => Volunteer::where('status', 'approved')->count(),
                'rejected_volunteers' => Volunteer::where('status', 'rejected')->count(),

                // Donations
                'total_donations' => Donation::sum('amount'),

                // Projects
                'active_projects' => Project::where('is_active', 1)->count(),

                // Programs
                'active_programs' => Program::where('is_active', 1)->count(),
            ]
        ]);
    }
}