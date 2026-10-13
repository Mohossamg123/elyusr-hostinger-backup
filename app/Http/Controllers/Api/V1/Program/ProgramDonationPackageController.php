<?php

namespace App\Http\Controllers\Api\V1\Program;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgramDonationPackageRequest;
use App\Http\Requests\UpdateProgramDonationPackageRequest;
use App\Http\Resources\ProgramDonationPackageResource;
use App\Models\Program;
use App\Models\ProgramDonationPackage;
use Illuminate\Http\JsonResponse;

class ProgramDonationPackageController extends Controller
{
    public function index(Program $program)
    {
        $packages = $program->donationPackages()
            ->orderBy('sort_order')
            ->get();

        return ProgramDonationPackageResource::collection($packages);
    }

    public function store(
        StoreProgramDonationPackageRequest $request,
        Program $program
    ): JsonResponse {
        $data = $request->validated();

        $data['program_id'] = $program->id;

        $package = ProgramDonationPackage::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Donation package created successfully',
            'data' => new ProgramDonationPackageResource($package),
        ], 201);
    }

    public function show(
        Program $program,
        ProgramDonationPackage $donation_package
    ): JsonResponse {
        abort_unless(
            $donation_package->program_id === $program->id,
            404
        );

        return response()->json([
            'success' => true,
            'data' => new ProgramDonationPackageResource(
                $donation_package
            ),
        ]);
    }

    public function update(
        UpdateProgramDonationPackageRequest $request,
        Program $program,
        ProgramDonationPackage $donation_package
    ): JsonResponse {
        abort_unless(
            $donation_package->program_id === $program->id,
            404
        );

        $donation_package->update(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Donation package updated successfully',
            'data' => new ProgramDonationPackageResource(
                $donation_package->fresh()
            ),
        ]);
    }

    public function destroy(
        Program $program,
        ProgramDonationPackage $donation_package
    ): JsonResponse {
        abort_unless(
            $donation_package->program_id === $program->id,
            404
        );

        $donation_package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Donation package deleted successfully',
        ]);
    }
}
