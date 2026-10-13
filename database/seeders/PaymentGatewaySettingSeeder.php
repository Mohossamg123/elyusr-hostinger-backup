<?php

namespace Database\Seeders;

use App\Models\PaymentGatewaySetting;
use Illuminate\Database\Seeder;

class PaymentGatewaySettingSeeder extends Seeder
{
    public function run(): void
    {
        PaymentGatewaySetting::firstOrCreate(
            ['gateway' => 'paymob'],
            [
                'is_active' => true,
                'credentials' => [
                    'base_url' => 'https://accept.paymob.com',
                    'secret_key' => config('services.paymob.secret_key'),
                    'api_key' => config('services.paymob.api_key'),
                    'integration_id' => config('services.paymob.integration_id'),
                    'currency' => config('services.paymob.currency', 'EGP'),
                    'notification_url' => config('services.paymob.notification_url'),
                    'redirection_url' => config('services.paymob.redirection_url'),
                ],
            ]
        );

        PaymentGatewaySetting::firstOrCreate(
            ['gateway' => 'kashier'],
            [
                'is_active' => true,
                'credentials' => [
                    'base_url' => config('services.kashier.base_url'),
                    'api_key' => config('services.kashier.api_key'),
                    'secret_key' => config('services.kashier.secret_key'),
                    'merchant_id' => config('services.kashier.merchant_id'),
                    'currency' => config('services.kashier.currency', 'EGP'),
                    'allowed_methods' => config('services.kashier.allowed_methods', 'card,wallet'),
                    'redirect_url' => config('services.kashier.redirect_url'),
                    'webhook_url' => config('services.kashier.webhook_url'),
                ],
            ]
        );
    }
}