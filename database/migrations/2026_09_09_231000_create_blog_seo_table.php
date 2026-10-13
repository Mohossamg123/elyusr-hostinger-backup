<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_seo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('content_id')
                ->unique()
                ->constrained('contents')
                ->cascadeOnDelete();

            $table->string('primary_keyword')->nullable();

            $table->string('category_ar')->nullable();
            $table->string('category_en')->nullable();

            $table->text('excerpt_en')->nullable();

            $table->json('tags_ar')->nullable();
            $table->json('tags_en')->nullable();

            $table->string('og_image')->nullable();
            $table->string('og_image_url')->nullable();

            $table->string('schema_type')->nullable()->default('Article');
            $table->json('schema_data')->nullable();

            $table->json('og_data')->nullable();

            $table->unsignedTinyInteger('seo_score')->default(0);

            $table->unsignedInteger('word_count_ar')->default(0);
            $table->unsignedInteger('word_count_en')->default(0);

            $table->unsignedInteger('title_length_ar')->default(0);
            $table->unsignedInteger('title_length_en')->default(0);

            $table->unsignedInteger('description_length_ar')->default(0);
            $table->unsignedInteger('description_length_en')->default(0);

            $table->boolean('has_schema')->default(false);
            $table->boolean('has_og')->default(false);
            $table->boolean('has_category')->default(false);
            $table->boolean('has_tags')->default(false);

            $table->enum('workflow_status', [
                'draft',
                'review',
                'published',
                'scheduled',
                'archived'
            ])->default('draft');

            $table->timestamp('scheduled_at')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['workflow_status', 'scheduled_at']);
            $table->index('seo_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_seo');
    }
};
