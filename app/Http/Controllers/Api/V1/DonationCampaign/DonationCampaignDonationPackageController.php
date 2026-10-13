<?php

namespace App\Http\Controllers\Api\V1\DonationCampaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgramDonationPackageRequest;
use App\Http\Requests\UpdateProgramDonationPackageRequest;
use App\Http\Resources\ProgramDonationPackageResource;
use App\Models\DonationCampaign;
use App\Models\ProgramDonationPackage;

class DonationCampaignDonationPackageController extends Controller
{
    public function index(DonationCampaign $donationCampaign)
    {
        return ProgramDonationPackageResource::collection(
            $donationCampaign->donationPackages()
                ->orderBy('sort_order')
                ->get()
        );
    }

    public function store(
        StoreProgramDonationPackageRequest $request,
        DonationCampaign $donationCampaign
    ) {
        $data = $request->validated();

        $data['program_id'] = null;
        $data['project_id'] = null;
        $data['donation_campaign_id'] = $donationCampaign->id;

        $package = ProgramDonationPackage::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Donation package created successfully',
            'data' => new ProgramDonationPackageResource($package),
        ], 201);
    }

    public function show(
        DonationCampaign $donationCampaign,
        ProgramDonationPackage $donation_package
    ) {
        abort_unless(
            $donation_package->donation_campaign_id
                === $donationCampaign->id,
            404
        );

        return new ProgramDonationPackageResource(
            $donation_package
        );
    }

    public function update(
        UpdateProgramDonationPackageRequest $request,
        DonationCampaign $donationCampaign,
        ProgramDonationPackage $donation_package
    ) {
        abort_unless(
            $donation_package->donation_campaign_id
                === $donationCampaign->id,
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
        DonationCampaign $donationCampaign,
        ProgramDonationPackage $donation_package
    ) {
        abort_unless(
            $donation_package->donation_campaign_id
                === $donationCampaign->id,
            404
        );

        $donation_package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Donation package deleted successfully',
        ]);
    }
}