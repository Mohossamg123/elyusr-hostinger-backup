<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgramDonationPackageRequest;
use App\Http\Requests\UpdateProgramDonationPackageRequest;
use App\Http\Resources\ProgramDonationPackageResource;
use App\Models\Project;
use App\Models\ProgramDonationPackage;

class ProjectDonationPackageController extends Controller
{
    public function index(Project $project)
    {
        return ProgramDonationPackageResource::collection(
            $project->donationPackages()
                ->orderBy('sort_order')
                ->get()
        );
    }

    public function store(
        StoreProgramDonationPackageRequest $request,
        Project $project
    ) {
        $data = $request->validated();

        $data['project_id'] = $project->id;
        $data['program_id'] = null;
        $data['donation_campaign_id'] = null;

        $package = ProgramDonationPackage::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Donation package created successfully',
            'data' => new ProgramDonationPackageResource($package),
        ], 201);
    }

    public function show(
        Project $project,
        ProgramDonationPackage $donation_package
    ) {
        abort_unless(
            $donation_package->project_id === $project->id,
            404
        );

        return new ProgramDonationPackageResource($donation_package);
    }

    public function update(
        UpdateProgramDonationPackageRequest $request,
        Project $project,
        ProgramDonationPackage $donation_package
    ) {
        abort_unless(
            $donation_package->project_id === $project->id,
            404
        );

        $donation_package->update(
            $request->validated()
        );

        return new ProgramDonationPackageResource(
            $donation_package->fresh()
        );
    }

    public function destroy(
        Project $project,
        ProgramDonationPackage $donation_package
    ) {
        abort_unless(
            $donation_package->project_id === $project->id,
            404
        );

        $donation_package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Donation package deleted successfully',
        ]);
    }
}