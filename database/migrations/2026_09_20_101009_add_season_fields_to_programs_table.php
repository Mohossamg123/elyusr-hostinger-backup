<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('period_ar')->nullable()->after('name_en');
            $table->string('period_en')->nullable()->after('period_ar');

            $table->string('tagline_ar')->nullable()->after('period_en');
            $table->string('tagline_en')->nullable()->after('tagline_ar');

            $table->boolean('donation_available')
                ->default(false)
                ->after('tagline_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'period_ar',
                'period_en',
                'tagline_ar',
                'tagline_en',
                'donation_available',
            ]);
        });
    }
};