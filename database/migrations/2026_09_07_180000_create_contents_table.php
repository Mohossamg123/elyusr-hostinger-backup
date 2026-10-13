<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            $table->enum('type', [
                'article',
                'program',
                'faq',
                'page',
            ])->index();

            $table->enum('status', [
                'draft',
                'published',
            ])->default('draft')->index();

            $table->string('slug')->unique();

            $table->string('title_ar');
            $table->string('title_en')->nullable();

            $table->text('summary_ar')->nullable();
            $table->text('summary_en')->nullable();

            $table->longText('content_ar')->nullable();
            $table->longText('content_en')->nullable();

            $table->string('cover_image')->nullable();
            $table->longText('svg')->nullable();

            $table->json('tags')->nullable();

            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
