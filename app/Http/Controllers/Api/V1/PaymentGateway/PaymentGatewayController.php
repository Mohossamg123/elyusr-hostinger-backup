<?php

namespace App\Http\Controllers\Api\V1\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Models\PaymentGatewaySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class PaymentGatewayController extends Controller
{
    private const GATEWAYS = [
        'paymob',
        'kashier',
    ];

    private const MASK = '********';

    public function index()
    {
        return response()->json([
            'data' => PaymentGatewaySetting::query()
                ->get()
                ->map(fn (PaymentGatewaySetting $setting) => $this->safeResponse($setting)),
        ]);
    }

    public function show(string $gateway)
    {
        $gateway = strtolower($gateway);

        $setting = PaymentGatewaySetting::query()
            ->where('gateway', $gateway)
            ->firstOrFail();

        return response()->json([
            'data' => $this->safeResponse($setting),
        ]);
    }

    public function update(Request $request, string $gateway)
    {
        $gateway = strtolower($gateway);

        abort_unless(
            in_array($gateway, self::GATEWAYS, true),
            404
        );

        $validated = $request->validate([
            'is_active' => ['sometimes', 'boolean'],
            'credentials' => ['sometimes', 'array'],
        ]);

        $setting = PaymentGatewaySetting::query()->firstOrCreate(
            ['gateway' => $gateway],
            [
                'is_active' => false,
                'credentials' => [],
            ]
        );

        if (array_key_exists('credentials', $validated)) {
            $existingCredentials = is_array($setting->credentials)
                ? $setting->credentials
                : [];

            $incomingCredentials = $validated['credentials'];

            /*
             * Frontend receives secrets as ********.
             * If it sends them back unchanged, do NOT overwrite
             * the real stored secret.
             */
            foreach ($incomingCredentials as $key => $value) {
                if ($value === self::MASK) {
                    unset($incomingCredentials[$key]);
                }
            }

            /*
             * Do not allow empty/null credentials to overwrite
             * an existing valid credential.
             */
            $incomingCredentials = array_filter(
                $incomingCredentials,
                static fn ($value) =>
                    $value !== null &&
                    $value !== ''
            );

            $validated['credentials'] = array_merge(
                $existingCredentials,
                $incomingCredentials
            );
        }

        $setting->update($validated);

        return response()->json([
            'message' => 'Payment gateway updated successfully',
            'data' => $this->safeResponse($setting->fresh()),
        ]);
    }

    public function toggle(string $gateway)
    {
        $gateway = strtolower($gateway);

        abort_unless(
            in_array($gateway, self::GATEWAYS, true),
            404
        );

        $setting = PaymentGatewaySetting::query()->firstOrCreate(
            ['gateway' => $gateway],
            [
                'is_active' => false,
                'credentials' => [],
            ]
        );

        $setting->update([
            'is_active' => !$setting->is_active,
        ]);

        return response()->json([
            'message' => 'Payment gateway status updated successfully',
            'data' => $this->safeResponse($setting->fresh()),
        ]);
    }

    public function testConnection(string $gateway)
    {
        $gateway = strtolower($gateway);

        abort_unless(
            in_array($gateway, self::GATEWAYS, true),
            404
        );

        $setting = PaymentGatewaySetting::query()
            ->where('gateway', $gateway)
            ->first();

        if (!$setting) {
            return response()->json([
                'message' => 'Payment gateway configuration not found.',
            ], 404);
        }

        $credentials = is_array($setting->credentials)
            ? $setting->credentials
            : [];

        try {
            return match ($gateway) {
                'kashier' => $this->testKashierConnection($credentials),
                'paymob' => $this->testPaymobConnection($credentials),
                default => response()->json([
                    'message' => 'Unsupported payment gateway.',
                ], 422),
            };
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Payment gateway connection test failed.',
            ], 502);
        }
    }

    private function testKashierConnection(array $credentials)
    {
        $baseUrl = $this->credential(
            $credentials,
            'base_url',
            config('services.kashier.base_url')
        );

        $secretKey = $this->credential(
            $credentials,
            'secret_key',
            config('services.kashier.secret_key')
        );

        $apiKey = $this->credential(
            $credentials,
            'api_key',
            config('services.kashier.api_key')
        );

        $merchantId = $this->credential(
            $credentials,
            'merchant_id',
            config('services.kashier.merchant_id')
        );

        $redirectUrl = $this->credential(
            $credentials,
            'redirect_url',
            config('services.kashier.redirect_url')
        );

        $webhookUrl = $this->credential(
            $credentials,
            'webhook_url',
            config('services.kashier.webhook_url')
        );

        if (empty($baseUrl)) {
            return $this->configurationError(
                'Kashier base URL is not configured.'
            );
        }

        if (empty($secretKey)) {
            return $this->configurationError(
                'Kashier secret key is not configured.'
            );
        }

        if (empty($apiKey)) {
            return $this->configurationError(
                'Kashier API key is not configured.'
            );
        }

        if (empty($merchantId)) {
            return $this->configurationError(
                'Kashier merchant ID is not configured.'
            );
        }

        if (empty($redirectUrl)) {
            return $this->configurationError(
                'Kashier redirect URL is not configured.'
            );
        }

        if (empty($webhookUrl)) {
            return $this->configurationError(
                'Kashier webhook URL is not configured.'
            );
        }

        /*
         * We intentionally do not create a payment session here.
         * Creating a session belongs to the real donation flow.
         *
         * The connection test therefore validates that all required
         * configuration exists without creating a real transaction.
         */
        return response()->json([
            'message' => 'Kashier configuration is valid.',
            'gateway' => 'kashier',
            'configured' => true,
        ]);
    }

    private function testPaymobConnection(array $credentials)
    {
        $baseUrl = $this->credential(
            $credentials,
            'base_url',
            config('services.paymob.base_url')
        );

        $secretKey = $this->credential(
            $credentials,
            'secret_key',
            config('services.paymob.secret_key')
        );

        $integrationId = $this->credential(
            $credentials,
            'integration_id',
            config('services.paymob.integration_id')
        );

        $notificationUrl = $this->credential(
            $credentials,
            'notification_url',
            config('services.paymob.notification_url')
        );

        $redirectionUrl = $this->credential(
            $credentials,
            'redirection_url',
            config('services.paymob.redirection_url')
        );

        if (empty($baseUrl)) {
            return $this->configurationError(
                'Paymob base URL is not configured.'
            );
        }

        if (empty($secretKey)) {
            return $this->configurationError(
                'Paymob secret key is not configured.'
            );
        }

        if (empty($integrationId)) {
            return $this->configurationError(
                'Paymob integration ID is not configured.'
            );
        }

        if (empty($notificationUrl)) {
            return $this->configurationError(
                'Paymob notification URL is not configured.'
            );
        }

        if (empty($redirectionUrl)) {
            return $this->configurationError(
                'Paymob redirection URL is not configured.'
            );
        }

        /*
         * We intentionally do not create a Paymob intention here.
         * Creating an intention belongs to the actual donation flow.
         */
        return response()->json([
            'message' => 'Paymob configuration is valid.',
            'gateway' => 'paymob',
            'configured' => true,
        ]);
    }

    private function credential(
        array $credentials,
        string $key,
        mixed $fallback = null
    ): mixed {
        if (
            array_key_exists($key, $credentials) &&
            $credentials[$key] !== null &&
            $credentials[$key] !== '' &&
            $credentials[$key] !== self::MASK
        ) {
            return $credentials[$key];
        }

        return $fallback;
    }

    private function configurationError(string $message)
    {
        return response()->json([
            'message' => $message,
            'configured' => false,
        ], 422);
    }

    private function safeResponse(
        PaymentGatewaySetting $setting
    ): array {
        $credentials = is_array($setting->credentials)
            ? $setting->credentials
            : [];

        /*
         * Never expose payment secrets through the API.
         */
        foreach ([
            'secret_key',
            'api_key',
            'public_key',
            'hmac_secret',
        ] as $key) {
            if (
                array_key_exists($key, $credentials) &&
                $credentials[$key] !== null &&
                $credentials[$key] !== ''
            ) {
                $credentials[$key] = self::MASK;
            }
        }

        return [
            'id' => $setting->id,
            'gateway' => $setting->gateway,
            'is_active' => $setting->is_active,
            'credentials' => $credentials,
            'created_at' => $setting->created_at,
            'updated_at' => $setting->updated_at,
        ];
    }
}