<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        $categories = DB::table('case_models')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->select('category')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $category) {
            $slug = Str::slug($category);

            if ($slug === '') {
                $slug = 'category-' . substr(md5($category), 0, 10);
            }

            $baseSlug = $slug;
            $counter = 1;

            while (DB::table('case_categories')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            DB::table('case_categories')->insert([
                'name_ar' => $category,
                'name_en' => null,
                'slug' => $slug,
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('case_models', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('category')
                ->constrained('case_categories')
                ->nullOnDelete();

            $table->index('category_id');
        });

        DB::statement("
            UPDATE case_models cm
            INNER JOIN case_categories cc
                ON cc.name_ar = cm.category
            SET cm.category_id = cc.id
            WHERE cm.category IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('case_models', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('case_categories');
    }
};
