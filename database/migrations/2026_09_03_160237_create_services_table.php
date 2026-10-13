<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_category_id')
                ->constrained('service_categories')
                ->cascadeOnDelete();

            $table->string('slug')->unique();

            $table->string('title_ar');
            $table->string('title_en');

            $table->string('badge_ar')->nullable();
            $table->string('badge_en')->nullable();

            $table->string('image_url')->nullable();

            $table->text('short_intro_ar')->nullable();
            $table->text('short_intro_en')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->json('donation_options')->nullable();

            $table->json('details_bullets')->nullable();

            $table->string('seo_title_ar')->nullable();
            $table->string('seo_title_en')->nullable();

            $table->text('seo_description_ar')->nullable();
            $table->text('seo_description_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
