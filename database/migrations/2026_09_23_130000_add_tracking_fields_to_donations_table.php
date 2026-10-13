<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('donations', 'transaction_reference')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->string('transaction_reference')
                    ->nullable()
                    ->unique()
                    ->after('id');
            });
        }

        if (!Schema::hasColumn('donations', 'status')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->string('status')
                    ->default('pending')
                    ->after('amount');
            });
        }

        if (!Schema::hasColumn('donations', 'payment_reference')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->string('payment_reference')
                    ->nullable()
                    ->after('status');
            });
        }

        if (!Schema::hasColumn('donations', 'payment_receipt')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->string('payment_receipt')
                    ->nullable()
                    ->after('payment_reference');
            });
        }

        if (!Schema::hasColumn('donations', 'payment_details')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->json('payment_details')
                    ->nullable()
                    ->after('payment_receipt');
            });
        }

        if (!Schema::hasColumn('donations', 'tracking_data')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->json('tracking_data')
                    ->nullable()
                    ->after('payment_details');
            });
        }
    }

    public function down(): void
    {
        $columns = [
            'transaction_reference',
            'status',
            'payment_reference',
            'payment_receipt',
            'payment_details',
            'tracking_data',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('donations', $column)) {
                Schema::table('donations', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};