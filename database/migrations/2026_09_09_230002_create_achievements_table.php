<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable("achievements")) {
            Schema::create("achievements", function (Blueprint $table) {
                $table->id();
                $table->string("title_ar")->nullable();
                $table->string("title_en")->nullable();
                $table->decimal("number", 15, 2)->default(0);
                $table->string("number_suffix_ar")->nullable();
                $table->string("number_suffix_en")->nullable();
                $table->string("icon")->nullable();
                $table->string("image")->nullable();
                $table->unsignedInteger("sort_order")->default(0);
                $table->boolean("is_active")->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable("achievements")) {
            Schema::dropIfExists("achievements");
        }
    }
};
