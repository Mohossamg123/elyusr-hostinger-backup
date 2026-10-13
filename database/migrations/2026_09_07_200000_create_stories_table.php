<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();

            $table->string('title_ar');
            $table->string('title_en')->nullable();

            $table->string('category_ar')->nullable();
            $table->string('category_en')->nullable();

            $table->string('beneficiary_name')->nullable();

            $table->string('location_ar')->nullable();
            $table->string('location_en')->nullable();

            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            $table->string('cover_image')->nullable();
            $table->string('cover_image_url')->nullable();

            $table->integer('sort_order')->default(0);

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_featured')->default(false);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
