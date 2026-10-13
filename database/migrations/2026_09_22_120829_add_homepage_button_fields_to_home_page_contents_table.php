<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_page_contents', function (Blueprint $table) {

            // Hero buttons
            $table->string('primary_cta_icon')->nullable()->after('primary_cta_url');
            $table->string('secondary_cta_icon')->nullable()->after('secondary_cta_url');

            // Section 2 buttons
            $table->string('section2_primary_cta_ar')->nullable();
            $table->string('section2_primary_cta_en')->nullable();
            $table->string('section2_primary_cta_url')->nullable();
            $table->string('section2_primary_cta_icon')->nullable();

            $table->string('section2_secondary_cta_ar')->nullable();
            $table->string('section2_secondary_cta_en')->nullable();
            $table->string('section2_secondary_cta_url')->nullable();
            $table->string('section2_secondary_cta_icon')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('home_page_contents', function (Blueprint $table) {

            $table->dropColumn([
                'primary_cta_icon',
                'secondary_cta_icon',

                'section2_primary_cta_ar',
                'section2_primary_cta_en',
                'section2_primary_cta_url',
                'section2_primary_cta_icon',

                'section2_secondary_cta_ar',
                'section2_secondary_cta_en',
                'section2_secondary_cta_url',
                'section2_secondary_cta_icon',
            ]);
        });
    }
};