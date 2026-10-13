<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('program_id')
                ->nullable()
                ->after('payment_method_id')
                ->constrained('programs')
                ->nullOnDelete();

            $table->foreignId('donation_package_id')
                ->nullable()
                ->after('program_id')
                ->constrained('program_donation_packages')
                ->nullOnDelete();

            $table->index(['program_id', 'donation_package_id']);
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['donation_package_id']);
            $table->dropForeign(['program_id']);

            $table->dropIndex(['program_id', 'donation_package_id']);

            $table->dropColumn([
                'program_id',
                'donation_package_id',
            ]);
        });
    }
};
