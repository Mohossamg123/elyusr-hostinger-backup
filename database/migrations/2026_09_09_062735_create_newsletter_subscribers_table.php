<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->enum('status', ['confirmed', 'pending', 'unsubscribed'])->default('confirmed');
            $table->string('language', 10)->default('ar');
            $table->string('source')->default('admin');
            $table->string('confirmation_token')->nullable()->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['language', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
