<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_campaign_packages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('donation_campaign_id')
                ->constrained('donation_campaigns')
                ->cascadeOnDelete();

            $table->string('name_ar');
            $table->string('name_en')->nullable();

            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            $table->decimal('amount', 10, 2);

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'donation_campaign_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_campaign_packages');
    }
};