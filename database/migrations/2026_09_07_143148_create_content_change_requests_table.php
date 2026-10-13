<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_change_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('module')->index();

            $table->string('action')->index();

            $table->string('method');

            $table->string('route_uri');

            $table->string('controller')->nullable();

            $table->string('controller_method')->nullable();

            $table->unsignedBigInteger('record_id')->nullable()->index();

            $table->json('payload')->nullable();

            $table->json('route_parameters')->nullable();

            $table->text('reason')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending')->index();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('review_note')->nullable();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_change_requests');
    }
};
