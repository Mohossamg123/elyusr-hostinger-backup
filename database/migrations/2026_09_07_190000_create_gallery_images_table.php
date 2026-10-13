<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('external_url')->nullable();

            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();

            $table->string('alt_ar')->nullable();
            $table->string('alt_en')->nullable();

            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            $table->string('city')->nullable();
            $table->string('program')->nullable();
            $table->string('donation_program')->nullable();

            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['is_public', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
