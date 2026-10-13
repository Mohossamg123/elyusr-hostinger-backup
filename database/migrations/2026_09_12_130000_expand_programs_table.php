<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('programs')) {
            return;
        }

        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }

            if (!Schema::hasColumn('programs', 'name_ar')) {
                $table->string('name_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'name_en')) {
                $table->string('name_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'description_ar')) {
                $table->text('description_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'description_en')) {
                $table->text('description_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'content_ar')) {
                $table->longText('content_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'content_en')) {
                $table->longText('content_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'benefits_ar')) {
                $table->json('benefits_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'benefits_en')) {
                $table->json('benefits_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'target_amount')) {
                $table->decimal('target_amount', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('programs', 'minimum_amount')) {
                $table->decimal('minimum_amount', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('programs', 'cover_image')) {
                $table->string('cover_image')->nullable();
            }

            if (!Schema::hasColumn('programs', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }

            if (!Schema::hasColumn('programs', 'meta_title_ar')) {
                $table->string('meta_title_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'meta_title_en')) {
                $table->string('meta_title_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'meta_description_ar')) {
                $table->text('meta_description_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'meta_description_en')) {
                $table->text('meta_description_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'keywords_ar')) {
                $table->text('keywords_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'keywords_en')) {
                $table->text('keywords_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'og_image')) {
                $table->string('og_image')->nullable();
            }

            if (!Schema::hasColumn('programs', 'og_title_ar')) {
                $table->string('og_title_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'og_title_en')) {
                $table->string('og_title_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'og_description_ar')) {
                $table->text('og_description_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'og_description_en')) {
                $table->text('og_description_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'canonical_url')) {
                $table->string('canonical_url')->nullable();
            }

            if (!Schema::hasColumn('programs', 'schema_type')) {
                $table->string('schema_type')->nullable();
            }

            if (!Schema::hasColumn('programs', 'twitter_card')) {
                $table->string('twitter_card')->nullable();
            }

            if (!Schema::hasColumn('programs', 'noindex')) {
                $table->boolean('noindex')->default(false);
            }

            if (!Schema::hasColumn('programs', 'nofollow')) {
                $table->boolean('nofollow')->default(false);
            }

            if (!Schema::hasColumn('programs', 'geo_region')) {
                $table->string('geo_region')->nullable();
            }

            if (!Schema::hasColumn('programs', 'geo_placename')) {
                $table->string('geo_placename')->nullable();
            }

            if (!Schema::hasColumn('programs', 'geo_position')) {
                $table->string('geo_position')->nullable();
            }

            if (!Schema::hasColumn('programs', 'geo_position_label')) {
                $table->string('geo_position_label')->nullable();
            }

            if (!Schema::hasColumn('programs', 'ai_summary_ar')) {
                $table->longText('ai_summary_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'ai_summary_en')) {
                $table->longText('ai_summary_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'ai_faq_ar')) {
                $table->longText('ai_faq_ar')->nullable();
            }

            if (!Schema::hasColumn('programs', 'ai_faq_en')) {
                $table->longText('ai_faq_en')->nullable();
            }

            if (!Schema::hasColumn('programs', 'hide_from_audience')) {
                $table->boolean('hide_from_audience')->default(false);
            }
        });
    }

    public function down(): void
    {
        // Intentionally left non-destructive to protect existing production data.
    }
};
