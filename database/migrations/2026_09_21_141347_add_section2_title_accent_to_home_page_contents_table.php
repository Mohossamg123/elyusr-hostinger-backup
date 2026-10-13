<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_page_contents', function (Blueprint $table) {
            $table->string('section2_title_accent_ar')->nullable()->after('section2_title_ar');
            $table->string('section2_title_accent_en')->nullable()->after('section2_title_accent_ar');
        });
    }

    public function down(): void
    {
        Schema::table('home_page_contents', function (Blueprint $table) {
            $table->dropColumn([
                'section2_title_accent_ar',
                'section2_title_accent_en',
            ]);
        });
    }
};
