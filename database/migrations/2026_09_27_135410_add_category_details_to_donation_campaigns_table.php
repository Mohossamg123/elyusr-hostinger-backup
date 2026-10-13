<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donation_campaigns', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('id')
                ->constrained('donation_campaign_categories')
                ->nullOnDelete();

            $table->string('subtitle_ar')->nullable()->after('slug');
            $table->string('subtitle_en')->nullable()->after('subtitle_ar');

            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('donation_campaigns', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            $table->dropColumn([
                'category_id',
                'subtitle_ar',
                'subtitle_en',
            ]);
        });
    }
};
