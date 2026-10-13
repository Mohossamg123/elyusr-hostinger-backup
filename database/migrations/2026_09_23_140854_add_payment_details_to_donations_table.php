<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_method_id');

            $table->string('payment_receipt')->nullable()->after('payment_reference');

            $table->json('payment_details')->nullable()->after('payment_receipt');

            // بيانات مندوب اليسر
            $table->string('pickup_governorate')->nullable()->after('payment_details');
            $table->string('pickup_city')->nullable()->after('pickup_governorate');
            $table->text('pickup_address')->nullable()->after('pickup_city');
            $table->string('pickup_preferred_time')->nullable()->after('pickup_address');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_reference',
                'payment_receipt',
                'payment_details',
                'pickup_governorate',
                'pickup_city',
                'pickup_address',
                'pickup_preferred_time',
            ]);
        });
    }
};