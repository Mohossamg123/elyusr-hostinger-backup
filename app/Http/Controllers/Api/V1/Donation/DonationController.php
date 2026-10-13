<?php

namespace App\Http\Controllers\Api\V1\Donation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Donation\StoreDonationRequest;
use App\Http\Requests\Donation\UpdateDonationRequest;
use App\Http\Resources\DonationResource;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\PaymentGatewaySetting;
use App\Models\User;
use App\Notifications\NewDonationNotification;
use App\Services\KashierService;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DonationController extends Controller
{
    public function index()
    {
        return DonationResource::collection(
            Donation::with([
                'donor',
                'paymentMethod',
                'program',
                'case',
                'donationPackage',
                'project',
                'donationCampaign',
            ])
                ->latest()
                ->paginate(10)
        );
    }
    
    

    public function store(
        StoreDonationRequest $request,
        PaymobService $paymobService,
        KashierService $kashierService
    ) {
        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Create donor automatically when donor_id is not provided
        |--------------------------------------------------------------------------
        */

        if (empty($data['donor_id'])) {
            $donor = Donor::firstOrCreate(
                [
                    'email' => $data['email'],
                ],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? null,
                ]
            );

            $data['donor_id'] = $donor->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Generate unique transaction reference
        |--------------------------------------------------------------------------
        */

        do {
            $transactionReference =
                'YSR-' .
                strtoupper(Str::random(8)) .
                '-' .
                random_int(1000, 9999);
        } while (
            Donation::where(
                'transaction_reference',
                $transactionReference
            )->exists()
        );

        $data['transaction_reference'] = $transactionReference;
        $data['status'] = 'pending';

        /*
        |--------------------------------------------------------------------------
        | Store payment receipt
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('payment_receipt')) {
            $data['payment_receipt'] = $request
                ->file('payment_receipt')
                ->store('donations/receipts', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Tracking data
        |--------------------------------------------------------------------------
        */

        $data['tracking_data'] = [
            'created_at' => now()->toISOString(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Remove request-only fields
        |--------------------------------------------------------------------------
        */
        
        
           unset(
                 $data['name'],
                 $data['email'],
                 $data['phone'],
                 $data['address'],
                 $data['_donation_target'],
                 $data['payment_reference'],
                 $data['payment_details']
                );


        /*
        |--------------------------------------------------------------------------
        | Create donation
        |--------------------------------------------------------------------------
        */

        $donation = Donation::create($data);

        /*
        |--------------------------------------------------------------------------
        | Load payment method
        |--------------------------------------------------------------------------
        */

        $donation->load([
            'donor',
            'paymentMethod',
        ]);

        $provider = strtolower(
            trim(
                $donation->paymentMethod?->provider_en ?? ''
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Validate payment method
        |--------------------------------------------------------------------------
        */

        if (
            !$donation->paymentMethod ||
            !$provider
        ) {
            $this->markPaymentFailed(
                $donation,
                'Invalid payment method or provider.'
            );

            return response()->json([
                'message' =>
                    'Selected payment method is invalid.',
                'transaction_reference' =>
                    $donation->transaction_reference,
            ], 422);
        }

        if (
            !in_array(
                $provider,
                ['paymob', 'kashier'],
                true
            )
        ) {
            $this->markPaymentFailed(
                $donation,
                'Unsupported payment provider: ' . $provider
            );

            return response()->json([
                'message' =>
                    'Selected payment provider is not supported.',
                'transaction_reference' =>
                    $donation->transaction_reference,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Check gateway configuration
        |--------------------------------------------------------------------------
        */

        $gateway = PaymentGatewaySetting::where(
            'gateway',
            $provider
        )->first();

        if (!$gateway) {
            $this->markPaymentFailed(
                $donation,
                ucfirst($provider) .
                    ' payment gateway is not configured.'
            );

            return response()->json([
                'message' =>
                    ucfirst($provider) .
                    ' payment gateway is not configured.',
                'transaction_reference' =>
                    $donation->transaction_reference,
            ], 422);
        }

        if (!$gateway->is_active) {
            $this->markPaymentFailed(
                $donation,
                ucfirst($provider) .
                    ' payment gateway is disabled.'
            );

            return response()->json([
                'message' =>
                    ucfirst($provider) .
                    ' payment gateway is currently disabled.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Check payment method status
        |--------------------------------------------------------------------------
        */

        if (!$donation->paymentMethod->is_active) {
            $this->markPaymentFailed(
                $donation,
                'Selected payment method is disabled.'
            );

            return response()->json([
                'message' =>
                    'Selected payment method is currently disabled.',
            ], 422);
        }

        $payment = [];

        /*
        |--------------------------------------------------------------------------
        | Create payment session / intention
        |--------------------------------------------------------------------------
        */

        try {
            if ($provider === 'paymob') {
                $paymobResponse =
                    $paymobService
                        ->createPaymentIntention(
                            $donation
                        );

                $clientSecret =
                    $paymobResponse['client_secret']
                    ?? null;

                if (
                    !is_string($clientSecret) ||
                    trim($clientSecret) === ''
                ) {
                    throw new \RuntimeException(
                        'Paymob did not return a valid client secret.'
                    );
                }

                $payment = [
                    'provider' => 'paymob',
                    'client_secret' => $clientSecret,
                ];
            }

            if ($provider === 'kashier') {
                $kashierResponse =
                    $kashierService
                        ->createPaymentSession(
                            $donation
                        );

                $sessionUrl =
                    $kashierResponse['sessionUrl']
                    ?? null;

                if (
                    !is_string($sessionUrl) ||
                    trim($sessionUrl) === ''
                ) {
                    throw new \RuntimeException(
                        'Kashier did not return a valid session URL.'
                    );
                }

                $payment = [
                    'provider' => 'kashier',
                    'session_url' => $sessionUrl,
                    'session_id' =>
                        $kashierResponse['_id']
                        ?? null,
                ];
            }
        } catch (Throwable $e) {
            report($e);

            $this->markPaymentFailed(
                $donation,
                'Payment initialization failed.',
                [
                    'exception' => get_class($e),
                ]
            );

            return response()->json([
                'message' =>
                    'Unable to initialize payment.',
                'transaction_reference' =>
                    $donation->transaction_reference,
            ], 502);
        }

        /*
        |--------------------------------------------------------------------------
        | Notify Super Admin after payment initialization
        |--------------------------------------------------------------------------
        */

        try {
            $donation->load('donor');

            $admins = User::whereHas(
                'roles',
                function ($query) {
                    $query->where(
                        'slug',
                        'super-admin'
                    );
                }
            )->get();

            foreach ($admins as $admin) {
                $admin->notify(
                    new NewDonationNotification(
                        $donation->id,
                        $donation->donor?->name ?? 'Donor',
                        (string) $donation->amount
                    )
                );
            }
        } catch (Throwable $e) {
            /*
             * Notification failure must not cancel a valid
             * payment session.
             */
            report($e);
        }

        /*
        |--------------------------------------------------------------------------
        | Return donation + payment information
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' =>
                'Donation created successfully',

            'data' => [
                'donation' =>
                    new DonationResource(
                        $donation->load([
                            'donor',
                            'paymentMethod',
                            'program',
                            'case',
                            'donationPackage',
                            'project',
                            'donationCampaign',
                        ])
                    ),

                'payment' => $payment,
            ],
        ], 201);
    }



    public function show(Donation $donation)
    {
        return new DonationResource(
            $donation->load([
                'donor',
                'paymentMethod',
                'program',
                'case',
                'donationPackage',
                'project',
                'donationCampaign',
            ])
        );
    }

    public function update(
        UpdateDonationRequest $request,
        Donation $donation
    ) {
        $data = $request->validated();
        
        
        
unset(
    $data['payment_reference'],
    $data['payment_details']
);

        
        /*
|--------------------------------------------------------------------------
| Do not allow editing financial data after payment
|--------------------------------------------------------------------------
*/


    unset(
        $data['amount'],
        $data['payment_method_id'],
        $data['program_id'],
        $data['case_id'],
        $data['project_id'],
        $data['donation_campaign_id'],
        $data['donation_package_id']
    );

        /*
        |--------------------------------------------------------------------------
        | Do not allow arbitrary payment-state changes from update endpoint
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['status']) &&
            $data['status'] !== $donation->status
        ) {
            unset($data['status']);
        }


       if (
    $request->hasFile('payment_receipt') &&
    !in_array($donation->status, ['paid', 'refunded'], true)
) {
    if ($donation->payment_receipt) {
        Storage::disk('public')->delete(
            $donation->payment_receipt
        );
    }

    $data['payment_receipt'] = $request
        ->file('payment_receipt')
        ->store(
            'donations/receipts',
            'public'
        );
}

        $donation->update($data);

        return new DonationResource(
            $donation->load([
                'donor',
                'paymentMethod',
                'program',
                'case',
                'donationPackage',
                'project',
                'donationCampaign',
            ])
        );
    }

  public function destroy(Donation $donation)
{
    if (
        in_array($donation->status, ['paid', 'refunded'], true)
    ) {
        return response()->json([
            'message' =>
                'Paid or refunded donations cannot be deleted.',
        ], 422);
    }

    if ($donation->payment_receipt) {
        Storage::disk('public')->delete(
            $donation->payment_receipt
        );
    }

    $donation->delete();

    return response()->json([
        'message' => 'Deleted Successfully',
    ]);
}
    
    

    public function track(string $transactionReference)
    {
        $donation = Donation::with([
            'program',
            'case',
            'donationPackage',
            'project',
            'donationCampaign',
            'paymentMethod',
        ])
            ->where(
                'transaction_reference',
                $transactionReference
            )
            ->first();

        if (!$donation) {
            return response()->json([
                'message' => 'Transaction not found.',
            ], 404);
        }

        return response()->json([
            'transaction_reference' =>
                $donation->transaction_reference,

            'status' =>
                $donation->status,

            'amount' =>
                $donation->amount,

            'program' => $donation->program
                ? [
                    'id' =>
                        $donation->program->id,

                    'name' =>
                        $donation->program->name,

                    'name_ar' =>
                        $donation->program->name_ar,

                    'name_en' =>
                        $donation->program->name_en,
                ]
                : null,

            'case' => $donation->case
                ? [
                    'id' =>
                        $donation->case->id,

                    'code' =>
                        $donation->case->code,

                    'title' =>
                        $donation->case->title,

                    'program_id' =>
                        $donation->case->program_id,
                ]
                : null,

            'project' => $donation->project
                ? [
                    'id' =>
                        $donation->project->id,

                    'name' =>
                        $donation->project->name,
                ]
                : null,

            'donation_campaign' =>
                $donation->donationCampaign
                    ? [
                        'id' =>
                            $donation
                                ->donationCampaign
                                ->id,

                        'name_ar' =>
                            $donation
                                ->donationCampaign
                                ->name_ar,

                        'name_en' =>
                            $donation
                                ->donationCampaign
                                ->name_en,

                        'slug' =>
                            $donation
                                ->donationCampaign
                                ->slug,
                    ]
                    : null,

            'donation_package' =>
                $donation->donationPackage
                    ? [
                        'id' =>
                            $donation
                                ->donationPackage
                                ->id,

                        'name_ar' =>
                            $donation
                                ->donationPackage
                                ->name_ar,

                        'name_en' =>
                            $donation
                                ->donationPackage
                                ->name_en,

                        'amount' =>
                            $donation
                                ->donationPackage
                                ->amount,
                    ]
                    : null,

            'payment_method' =>
                $donation->paymentMethod
                    ? [
                        'id' =>
                            $donation
                                ->paymentMethod
                                ->id,

                        'name_ar' =>
                            $donation
                                ->paymentMethod
                                ->name_ar,

                        'name_en' =>
                            $donation
                                ->paymentMethod
                                ->name_en,

                        'slug' =>
                            $donation
                                ->paymentMethod
                                ->slug,
                    ]
                    : null,

            'created_at' =>
                $donation->created_at,
        ]);
    }

    protected function markPaymentFailed(
        Donation $donation,
        string $message,
        array $extra = []
    ): void {
        $trackingData = array_merge(
            $donation->tracking_data ?? [],
            [
                'payment_error' => $message,
                'failed_at' =>
                    now()->toISOString(),
            ],
            $extra
        );

        $donation->update([
            'status' => 'failed',
            'tracking_data' => $trackingData,
        ]);
    }
}