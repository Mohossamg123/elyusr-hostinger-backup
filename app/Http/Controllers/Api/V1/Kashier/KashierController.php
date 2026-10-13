<?php

namespace App\Http\Controllers\Api\V1\Kashier;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\PaymentGatewaySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class KashierController extends Controller
{
    public function webhook(Request $request)
    {
        $payload = $request->all();

        $data = $payload['data'] ?? [];

        if (!is_array($data) || empty($data)) {
            return response()->json([
                'message' => 'Invalid webhook data.',
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Kashier Signature
        |--------------------------------------------------------------------------
        */

        if (!$this->verifySignature($request, $data)) {
            return response()->json([
                'message' => 'Invalid Kashier signature.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Transaction Reference
        |--------------------------------------------------------------------------
        */



$transactionReference = $data['merchantOrderId'] ?? null;
        if (
            !is_string($transactionReference) ||
            trim($transactionReference) === ''
        ) {
            return response()->json([
                'message' => 'Transaction reference not found.',
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Status
        |--------------------------------------------------------------------------
        */

        $status = strtoupper(
            trim((string) ($data['status'] ?? ''))
        );

        if (!in_array(
            $status,
            ['SUCCESS', 'FAILURE', 'PENDING'],
            true
        )) {
            return response()->json([
                'message' => 'Unsupported Kashier payment status.',
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Process Webhook Atomically
        |--------------------------------------------------------------------------
        */

        try {
         $result = DB::transaction(function () use (
    $transactionReference,
    $data,
    $status,
    $payload
) 

    {
                /*
                |--------------------------------------------------------------------------
                | Lock Donation
                |--------------------------------------------------------------------------
                */

                $donation = Donation::where(
                    'transaction_reference',
                    $transactionReference
                )
                    ->lockForUpdate()
                    ->first();

                if (!$donation) {
                    return [
                        'type' => 'not_found',
                    ];
                }

             /*
|--------------------------------------------------------------------------
| Handle Refund Events
|--------------------------------------------------------------------------
*/

$event = strtolower(
    trim((string) ($payload['event'] ?? $data['event'] ?? ''))
);

$isRefundEvent = in_array(
    $event,
    ['refund', 'partial_refund'],
    true
);

if ($isRefundEvent) {
    if ($donation->status === 'refunded') {
        return [
            'type' => 'already_processed',
        ];
    }

    if ($status !== 'SUCCESS') {
        return [
            'type' => 'refund_not_successful',
        ];
    }

    $trackingData = $donation->tracking_data ?? [];

    $trackingData = array_merge(
        $trackingData,
        [
            'kashier_event' => $event,
            'payment_status' => 'refunded',
            'refunded_at' => now()->toISOString(),
            'kashier_transaction_id' =>
                $data['transactionId']
                ?? $trackingData['kashier_transaction_id']
                ?? null,
        ]
    );

    $donation->update([
        'status' => 'refunded',
        'payment_details' => $data,
        'tracking_data' => $trackingData,
    ]);

    return [
        'type' => 'refunded',
    ];
}

/*
|--------------------------------------------------------------------------
| Prevent Invalid Final State Changes
|--------------------------------------------------------------------------
*/

if (
    in_array(
        $donation->status,
        ['paid', 'refunded'],
        true
    )
) {
    return [
        'type' => 'already_processed',
    ];
}

                /*
                |--------------------------------------------------------------------------
                | Validate Amount
                |--------------------------------------------------------------------------
                */

                $expectedAmount = number_format(
                    (float) $donation->amount,
                    2,
                    '.',
                    ''
                );

                $receivedAmount =
                    $data['amount']
                    ?? $data['orderAmount']
                    ?? null;

                if (
                    $receivedAmount === null ||
                    $receivedAmount === '' ||
                    !is_numeric($receivedAmount)
                ) {
                    return [
                        'type' => 'amount_mismatch',
                    ];
                }

                $receivedAmountFormatted = number_format(
                    (float) $receivedAmount,
                    2,
                    '.',
                    ''
                );

                if (
                    $receivedAmountFormatted !==
                    $expectedAmount
                ) {
                    return [
                        'type' => 'amount_mismatch',
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Currency
                |--------------------------------------------------------------------------
                */

                $currency = strtoupper(
                    trim((string) (
                        $data['currency']
                        ?? $data['orderCurrency']
                        ?? ''
                    ))
                );

                if ($currency === '') {
                    return [
                        'type' => 'currency_mismatch',
                    ];
                }

                if ($currency !== 'EGP') {
                    return [
                        'type' => 'currency_mismatch',
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Pending
                |--------------------------------------------------------------------------
                */

                if ($status === 'PENDING') {
                    $trackingData = $donation->tracking_data ?? [];

                    $trackingData = array_merge(
                        $trackingData,
                        [
                            'kashier_transaction_id' =>
                                $data['transactionId']
                                ?? $trackingData[
                                    'kashier_transaction_id'
                                ]
                                ?? null,

                            'kashier_order_id' =>
                                $data['kashierOrderId']
                                ?? $trackingData[
                                    'kashier_order_id'
                                ]
                                ?? null,

                            'payment_status' => 'pending',

                            'updated_at' =>
                                now()->toISOString(),
                        ]
                    );

                    $donation->update([
                        'tracking_data' => $trackingData,
                        'payment_details' => $data,
                    ]);

                    return [
                        'type' => 'pending',
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | SUCCESS / FAILURE
                |--------------------------------------------------------------------------
                */

                $finalStatus =
                    $status === 'SUCCESS'
                        ? 'paid'
                        : 'failed';

                $trackingData = $donation->tracking_data ?? [];

                $trackingData = array_merge(
                    $trackingData,
                    [
                        'kashier_transaction_id' =>
                            $data['transactionId']
                            ?? null,

                        'kashier_order_id' =>
                            $data['kashierOrderId']
                            ?? null,

                        'payment_status' =>
                            $finalStatus,

                        'updated_at' =>
                            now()->toISOString(),
                    ]
                );

                $paymentReference =
                    $data['transactionId']
                    ?? $donation->payment_reference;

                $donation->update([
                    'status' => $finalStatus,

                    'payment_reference' =>
                        $paymentReference,

                    'payment_details' =>
                        $data,

                    'tracking_data' =>
                        $trackingData,
                ]);

                return [
                    'type' => 'processed',
                    'status' => $finalStatus,
                ];
            });

            /*
            |--------------------------------------------------------------------------
            | Transaction Result
            |--------------------------------------------------------------------------
            */

            if ($result['type'] === 'not_found') {
                return response()->json([
                    'message' => 'Donation not found.',
                ], 404);
            }

            if ($result['type'] === 'already_processed') {
                return response()->json([
                    'message' => 'Webhook already processed.',
                ], 200);
            }

            if ($result['type'] === 'amount_mismatch') {
                return response()->json([
                    'message' => 'Payment amount mismatch.',
                ], 422);
            }

            if ($result['type'] === 'currency_mismatch') {
                return response()->json([
                    'message' => 'Unsupported payment currency.',
                ], 422);
            }

            if ($result['type'] === 'pending') {
                return response()->json([
                    'message' => 'Payment is still pending.',
                ], 200);
            }
            
            if ($result['type'] === 'refunded') {
    return response()->json([
        'message' => 'Refund processed successfully.',
    ], 200);
}

if ($result['type'] === 'refund_not_successful') {
    return response()->json([
        'message' => 'Refund was not successful.',
    ], 200);
}
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' =>
                    'Failed to process Kashier webhook.',
            ], 500);
        }

        return response()->json([
            'message' => 'Webhook processed successfully.',
        ], 200);
    }
    
public function redirect(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | Verify Kashier Redirect Signature
    |--------------------------------------------------------------------------
    */

    $data = [
        'paymentStatus' => $request->query('paymentStatus'),
        'cardDataToken' => $request->query('cardDataToken'),
        'maskedCard' => $request->query('maskedCard'),
        'merchantOrderId' => $request->query('merchantOrderId'),
        'orderId' => $request->query('orderId'),
        'cardBrand' => $request->query('cardBrand'),
        'orderReference' => $request->query('orderReference'),
        'transactionId' => $request->query('transactionId'),
        'amount' => $request->query('amount'),
        'currency' => $request->query('currency'),
    ];

    $receivedSignature = $request->query('signature');

    if (
        !is_string($receivedSignature) ||
        trim($receivedSignature) === ''
    ) {
        return response()->json([
            'message' => 'Invalid Kashier signature.',
        ], 403);
    }

    /*
    |--------------------------------------------------------------------------
    | Kashier Redirect Signature
    |--------------------------------------------------------------------------
    |
    | Fixed field order according to Kashier.
    |
    */
$signaturePayload = implode('&', array_map(
    function ($key) use ($data) {
        $value = $data[$key];

        return $key . '=' . (
            $value === null || $value === ''
                ? 'null'
                : (string) $value
        );
    },
    [
        'paymentStatus',
        'cardDataToken',
        'maskedCard',
        'merchantOrderId',
        'orderId',
        'cardBrand',
        'orderReference',
        'transactionId',
        'amount',
        'currency',
    ]
));

    /*
    |--------------------------------------------------------------------------
    | Resolve Payment API Key
    |--------------------------------------------------------------------------
    */

    $setting = PaymentGatewaySetting::where(
        'gateway',
        'kashier'
    )
        ->where('is_active', true)
        ->first();

    $credentials = is_array($setting?->credentials)
        ? $setting->credentials
        : [];

    $apiKey = $credentials['api_key']
        ?? config('services.kashier.api_key');

    if (
        !is_string($apiKey) ||
        trim($apiKey) === ''
    ) {
        return response()->json([
            'message' => 'Kashier payment API key is not configured.',
        ], 500);
    }

    $calculatedSignature = hash_hmac(
        'sha256',
        $signaturePayload,
        trim($apiKey)
    );

    if (!hash_equals(
        strtolower($calculatedSignature),
        strtolower(trim($receivedSignature))
    )) {
        return response()->json([
            'message' => 'Invalid Kashier signature.',
        ], 403);
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Donation
    |--------------------------------------------------------------------------
    */

    $transactionReference =
        $request->query('merchantOrderId')
        ?? $request->query('orderReference');

    if (
        !is_string($transactionReference) ||
        trim($transactionReference) === ''
    ) {
        return response()->json([
            'message' => 'Transaction reference not found.',
        ], 400);
    }

    $donation = Donation::where(
        'transaction_reference',
        $transactionReference
    )->first();

    if (!$donation) {
        return response()->json([
            'message' => 'Donation not found.',
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Amount
    |--------------------------------------------------------------------------
    */

    $receivedAmount = $request->query('amount');

    if (
        $receivedAmount === null ||
        !is_numeric($receivedAmount)
    ) {
        return response()->json([
            'message' => 'Invalid payment amount.',
        ], 422);
    }

    $expectedAmount = number_format(
        (float) $donation->amount,
        2,
        '.',
        ''
    );

    $receivedAmountFormatted = number_format(
        (float) $receivedAmount,
        2,
        '.',
        ''
    );

    if ($receivedAmountFormatted !== $expectedAmount) {
        return response()->json([
            'message' => 'Payment amount mismatch.',
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Currency
    |--------------------------------------------------------------------------
    */

    $currency = strtoupper(
        trim((string) $request->query('currency'))
    );

    if ($currency !== 'EGP') {
        return response()->json([
            'message' => 'Payment currency mismatch.',
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect is informational only
    |--------------------------------------------------------------------------
    |
    | Do NOT mark the donation as paid here.
    | Kashier webhook remains the source of truth.
    |
    */

    return response()->json([
        'message' => 'Kashier payment redirect received.',
        'data' => [
            'transaction_reference' =>
                $donation->transaction_reference,

            'payment_status' =>
                $request->query('paymentStatus'),

            'amount' =>
                $receivedAmountFormatted,

            'currency' =>
                $currency,

            'transaction_id' =>
                $request->query('transactionId'),

            'order_id' =>
                $request->query('orderId'),
        ],
    ]);
}

    protected function verifySignature(
        Request $request,
        array $data
    ): bool {
        /*
        |--------------------------------------------------------------------------
        | Received Signature
        |--------------------------------------------------------------------------
        */

        $receivedSignature = $request->header(
            'x-kashier-signature'
        );

        if (
            !is_string($receivedSignature) ||
            trim($receivedSignature) === ''
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Signature Keys
        |--------------------------------------------------------------------------
        */

        $signatureKeys =
            $data['signatureKeys'] ?? [];

        if (
            !is_array($signatureKeys) ||
            empty($signatureKeys)
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Kashier Payment Webhook Signature
        |--------------------------------------------------------------------------
        |
        | Kashier payment webhooks require:
        |
        | 1. Sort signatureKeys alphabetically.
        | 2. Select only those fields.
        | 3. URL-encode values only.
        | 4. Join using &.
        | 5. HMAC SHA256 using Payment API Key.
        |
        */

        $signatureKeys = array_values(
            array_filter(
                $signatureKeys,
                static fn ($key) =>
                    is_string($key) &&
                    trim($key) !== ''
            )
        );

        sort($signatureKeys, SORT_STRING);

        if (empty($signatureKeys)) {
            return false;
        }

        $parts = [];

        foreach ($signatureKeys as $key) {
            /*
            |--------------------------------------------------------------------------
            | Only sign keys actually present in data
            |--------------------------------------------------------------------------
            */

            if (!array_key_exists($key, $data)) {
                return false;
            }

            $value = $data[$key];

            if (is_array($value)) {
                $encodedValue = json_encode(
                    $value,
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
                );

                if ($encodedValue === false) {
                    return false;
                }

                $value = $encodedValue;
            } elseif (is_bool($value)) {
                $value = $value
                    ? 'true'
                    : 'false';
            } elseif ($value === null) {
                $value = 'null';
            } else {
                $value = (string) $value;
            }

            $parts[] =
                $key .
                '=' .
                rawurlencode($value);
        }

        if (empty($parts)) {
            return false;
        }

        $signaturePayload = implode(
            '&',
            $parts
        );

        /*
        |--------------------------------------------------------------------------
        | Resolve Payment API Key
        |--------------------------------------------------------------------------
        */

        $setting = PaymentGatewaySetting::where(
            'gateway',
            'kashier'
        )
            ->where('is_active', true)
            ->first();

        $credentials = is_array(
            $setting?->credentials
        )
            ? $setting->credentials
            : [];

        $apiKey =
            $credentials['api_key']
            ?? config('services.kashier.api_key');

        if (
            !is_string($apiKey) ||
            trim($apiKey) === ''
        ) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate HMAC
        |--------------------------------------------------------------------------
        */

        $calculatedSignature = hash_hmac(
            'sha256',
            $signaturePayload,
            trim($apiKey)
        );

        return hash_equals(
            strtolower(
                $calculatedSignature
            ),
            strtolower(
                trim($receivedSignature)
            )
        );
    }
}