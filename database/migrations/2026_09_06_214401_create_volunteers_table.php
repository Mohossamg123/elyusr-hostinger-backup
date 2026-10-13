<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone')->index();
            $table->string('governorate')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('address')->nullable();
            $table->json('skills')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'inactive',
            ])->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};
