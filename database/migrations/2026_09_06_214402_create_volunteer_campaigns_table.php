<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('governorate')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('location')->nullable();
            $table->unsignedInteger('required_volunteers')->default(1);
            $table->json('required_skills')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->enum('status', [
                'draft',
                'open',
                'closed',
                'cancelled',
            ])->default('draft')->index();
            $table->timestamps();

            $table->index(['event_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_campaigns');
    }
};
