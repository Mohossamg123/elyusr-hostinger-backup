<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            
            
            // باي موب
            [
                'name_ar' => 'بطاقة بنكية',
                'name_en' => 'Credit Card',
                'slug' => 'credit-card',
                'type' => 'card',
                'provider_ar' => 'Paymob',
                'provider_en' => 'Paymob',
                'details' => [
                    'cards' => ['Visa', 'Mastercard'],
                    'gateway' => 'Paymob',
                    'secure' => true,
                ],
                'description_ar' => 'الدفع الآمن باستخدام بطاقات فيزا وماستركارد عبر بوابة Paymob.',
                'description_en' => 'Secure payment using Visa and Mastercard through Paymob gateway.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            
            
            // كاشير
            [
    'name_ar' => 'بطاقة بنكية - كاشير',

    'name_en' => 'Credit Card - Kashier',

    'slug' => 'credit-card-kashier',

    'type' => 'card',

    'provider_ar' => 'Kashier',

    'provider_en' => 'Kashier',

    'details' => [
        'cards' => [
            'Visa',
            'Mastercard',
        ],

        'wallets' => true,

        'gateway' => 'Kashier',

        'secure' => true,
    ],

    'description_ar' =>
        'الدفع الآمن باستخدام البطاقات والمحافظ الإلكترونية عبر بوابة كاشير.',

    'description_en' =>
        'Secure payment using cards and wallets through Kashier gateway.',

    'sort_order' => 6,

    'is_active' => true,
],

            
            [
                'name_ar' => 'تحويل بنكي',
                'name_en' => 'Bank Transfer',
                'slug' => 'bank-transfer',
                'type' => 'bank_transfer',
                'provider_ar' => 'بنك مصر',
                'provider_en' => 'Bank Misr',
                'details' => [
                    'bank_name' => 'بنك مصر',
                    'iban' => '',
                    'account_number' => '',
                ],
                'description_ar' => 'التحويل المباشر إلى الحساب البنكي الخاص بالمؤسسة.',
                'description_en' => 'Direct transfer to the organization bank account.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name_ar' => 'فودافون كاش',
                'name_en' => 'Vodafone Cash',
                'slug' => 'vodafone-cash',
                'type' => 'wallet',
                'provider_ar' => 'Vodafone Cash',
                'provider_en' => 'Vodafone Cash',
                'details' => [
                    'wallet_number' => '',
                ],
                'description_ar' => 'التبرع من خلال محفظة فودافون كاش.',
                'description_en' => 'Donate using Vodafone Cash wallet.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name_ar' => 'إنستا باي',
                'name_en' => 'InstaPay',
                'slug' => 'instapay',
                'type' => 'instant_payment',
                'provider_ar' => 'InstaPay',
                'provider_en' => 'InstaPay',
                'details' => [
                    'ipa' => '',
                ],
                'description_ar' => 'التبرع الفوري باستخدام تطبيق إنستا باي.',
                'description_en' => 'Instant donation using InstaPay.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name_ar' => 'مندوب تحصيل البيت',
                'name_en' => 'Home Pickup Delegate',
                'slug' => 'home-pickup',
                'type' => 'cash_pickup',
                'provider_ar' => 'مندوب تحصيل',
                'provider_en' => 'Collection Delegate',
                'details' => [
                    'availability' => true,
                    'pickup_required' => true,
                ],
                'description_ar' => 'طلب مندوب لتحصيل التبرع من المنزل.',
                'description_en' => 'Request a delegate to collect the donation from home.',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['slug' => $method['slug']],
                $method
            );
        }
    }
}
