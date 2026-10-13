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
        Schema::create('case_models', function (Blueprint $table) {

    $table->id();

    $table->string('code')->unique();

    $table->string('title');

    $table->string('category');

    $table->string('beneficiary_name');

    $table->integer('age')->nullable();

    $table->string('governorate');

    $table->string('city')->nullable();

    $table->decimal('target_amount',10,2);

    $table->date('deadline')->nullable();

    $table->enum('priority',['low','medium','high'])->default('medium');

    $table->string('cover_image')->nullable();

    $table->text('description');

    $table->enum('status',[
        'pending_review',
        'approved',
        'completed',
        'closed',
        'rejected'
    ])->default('pending_review');

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_models');
    }
};
