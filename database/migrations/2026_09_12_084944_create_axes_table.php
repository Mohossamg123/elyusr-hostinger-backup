<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('axes')) {
            Schema::create('axes', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name_ar');
                $table->string('name_en');
                $table->text('description_ar')->nullable();
                $table->text('description_en')->nullable();
                $table->string('image')->nullable();
                $table->string('badge_ar')->nullable();
                $table->string('badge_en')->nullable();
                $table->json('statistics')->nullable();
                $table->json('features_ar')->nullable();
                $table->json('features_en')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['is_active', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('axes');
    }
};
