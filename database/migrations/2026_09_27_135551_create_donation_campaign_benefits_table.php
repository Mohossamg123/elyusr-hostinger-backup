<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_campaign_benefits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('donation_campaign_id')
                ->constrained('donation_campaigns')
                ->cascadeOnDelete();

            $table->string('title_ar');
            $table->string('title_en')->nullable();

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'donation_campaign_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_campaign_benefits');
    }
};
