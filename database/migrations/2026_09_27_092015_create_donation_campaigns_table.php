<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_campaigns', function (Blueprint $table) {
            $table->id();

            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();

            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            $table->string('image')->nullable();

            $table->decimal('target_amount', 12, 2)->nullable();
            $table->decimal('collected_amount', 12, 2)->default(0);
            $table->decimal('minimum_amount', 10, 2)->nullable();

            $table->boolean('donation_available')->default(true);
            $table->boolean('is_active')->default(true);

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_campaigns');
    }
};