<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {

            $table->foreignId('case_id')
                ->nullable()
                ->after('program_id')
                ->constrained('case_models')
                ->nullOnDelete();

            $table->index([
                'program_id',
                'case_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {

            $table->dropForeign(['case_id']);

            $table->dropIndex([
                'program_id',
                'case_id',
            ]);

            $table->dropColumn('case_id');
        });
    }
};