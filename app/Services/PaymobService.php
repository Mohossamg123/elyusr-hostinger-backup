<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\PaymentGatewaySetting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaymobService
{
    protected array $settings = [];

    public function __construct()
    {
        $setting = PaymentGatewaySetting::where('gateway', 'paymob')
            ->where('is_active', true)
            ->first();

        $this->settings = is_array($setting?->credentials)
            ? $setting->credentials
            : [];
    }

    public function createPaymentIntention(Donation $donation): array
    {
        $donation->loadMissing('donor');

        if (!$donation->transaction_reference) {
            throw new RuntimeException(
                'Donation transaction reference is missing.'
            );
        }

        if ((float) $donation->amount <= 0) {
            throw new RuntimeException(
                'Donation amount must be greater than zero.'
            );
        }

        $secretKey = $this->setting('secret_key', 'services.paymob.secret_key');
        $integrationId = $this->setting('integration_id', 'services.paymob.integration_id');

        $baseUrl = rtrim(
            (string) $this->setting(
                'base_url',
                'services.paymob.base_url',
                'https://accept.paymob.com'
            ),
            '/'
        );

        $currency = strtoupper(
            (string) $this->setting(
                'currency',
                'services.paymob.currency',
                'EGP'
            )
        );

        $notificationUrl = $this->setting(
            'notification_url',
            'services.paymob.notification_url'
        );

        $redirectionUrl = $this->setting(
            'redirection_url',
            'services.paymob.redirection_url'
        );

        if ($baseUrl === '') {
            throw new RuntimeException('Paymob base URL is not configured.');
        }

        if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Paymob base URL is invalid.');
        }

        if (!is_string($secretKey) || trim($secretKey) === '') {
            throw new RuntimeException('Paymob secret key is not configured.');
        }

        if (
            !is_numeric($integrationId) ||
            (int) $integrationId <= 0
        ) {
            throw new RuntimeException('Paymob integration ID is invalid.');
        }

        if ($currency !== 'EGP') {
            throw new RuntimeException('Paymob currency must be EGP.');
        }

        if (
            !is_string($notificationUrl) ||
            !filter_var($notificationUrl, FILTER_VALIDATE_URL)
        ) {
            throw new RuntimeException(
                'Paymob notification URL is not configured or invalid.'
            );
        }

        if (
            !is_string($redirectionUrl) ||
            !filter_var($redirectionUrl, FILTER_VALIDATE_URL)
        ) {
            throw new RuntimeException(
                'Paymob redirection URL is not configured or invalid.'
            );
        }

        $amountInCents = (int) round(
            ((float) $donation->amount) * 100
        );

        if ($amountInCents <= 0) {
            throw new RuntimeException(
                'Donation amount must be greater than zero.'
            );
        }

        $donor = $donation->donor;

        $email = is_string($donor?->email)
            && filter_var($donor->email, FILTER_VALIDATE_EMAIL)
            ? $donor->email
            : 'no-reply@elyusr.org';

        $phone = is_string($donor?->phone)
            && trim($donor->phone) !== ''
            ? trim($donor->phone)
            : 'NA';

        $address = is_string($donor?->address)
            && trim($donor->address) !== ''
            ? trim($donor->address)
            : 'NA';

        $payload = [
            'amount' => $amountInCents,
            'currency' => $currency,
            'payment_methods' => [
                (int) $integrationId,
            ],
            'items' => [
                [
                    'name' => 'Donation',
                    'amount' => $amountInCents,
                    'description' => 'Donation to Al Yusr',
                    'quantity' => 1,
                ],
            ],
            'billing_data' => [
                'apartment' => 'NA',
                'floor' => 'NA',
                'first_name' => $this->firstName($donor?->name),
                'last_name' => $this->lastName($donor?->name),
                'street' => $address,
                'building' => 'NA',
                'phone_number' => $phone,
                'city' => 'NA',
                'country' => 'EG',
                'state' => 'NA',
                'email' => $email,
            ],
            'special_reference' => $donation->transaction_reference,
            'expiration' => 3600,
            'notification_url' => $notificationUrl,
            'redirection_url' => $redirectionUrl,
        ];

        try {
            $response = Http::timeout(30)
                ->connectTimeout(10)
                ->withHeaders([
    'Authorization' => 'Token ' . trim($secretKey),
    'Accept' => 'application/json',
    'Content-Type' => 'application/json',
])
                ->acceptJson()
                ->asJson()
                ->post(
                    $baseUrl . '/v1/intention/',
                    $payload
                );
        } catch (\Throwable $e) {
            report($e);

            throw new RuntimeException(
                'Unable to connect to Paymob.'
            );
        }

        if ($response->failed()) {
            report(new RuntimeException(
                'Paymob payment intention failed with HTTP ' .
                $response->status() . ': ' . $response->body()
            ));

            throw new RuntimeException(
                'Paymob payment intention failed.'
            );
        }

        $result = $response->json();

        if (!is_array($result)) {
            throw new RuntimeException(
                'Invalid response received from Paymob.'
            );
        }

        $clientSecret = $result['client_secret'] ?? null;

        if (
            !is_string($clientSecret) ||
            trim($clientSecret) === ''
        ) {
            throw new RuntimeException(
                'Paymob did not return a valid client secret.'
            );
        }

        return $result;
    }

    protected function setting(
        string $key,
        string $configKey,
        mixed $default = null
    ): mixed {
        $value = $this->settings[$key] ?? null;

        if ($value !== null && $value !== '') {
            return $value;
        }

        return config($configKey, $default);
    }

    protected function firstName(?string $name): string
    {
        if (!$name || trim($name) === '') {
            return 'Donor';
        }

        $parts = preg_split('/\s+/', trim($name));

        return $parts[0] ?? 'Donor';
    }

    protected function lastName(?string $name): string
    {
        if (!$name || trim($name) === '') {
            return 'Customer';
        }

        $parts = preg_split('/\s+/', trim($name));

        if (count($parts) <= 1) {
            return 'Customer';
        }

        array_shift($parts);

        return implode(' ', $parts);
    }
}
