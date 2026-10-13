<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_campaign_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained('volunteer_campaigns')
                ->cascadeOnDelete();

            $table->foreignId('volunteer_id')
                ->constrained('volunteers')
                ->cascadeOnDelete();

            $table->enum('status', [
                'invited',
                'pending',
                'approved',
                'rejected',
                'withdrawn',
            ])->default('pending')->index();

            $table->timestamp('invited_at')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['campaign_id', 'volunteer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_campaign_applications');
    }
};
