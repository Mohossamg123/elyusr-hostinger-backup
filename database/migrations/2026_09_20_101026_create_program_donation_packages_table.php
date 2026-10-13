<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_donation_packages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();

            $table->string('name_ar');
            $table->string('name_en')->nullable();

            $table->decimal('amount', 12, 2);

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['program_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_donation_packages');
    }
};