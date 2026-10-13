<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->foreignId('season_id')
                ->nullable()
                ->after('axis_id')
                ->constrained('seasons')
                ->nullOnDelete();

            $table->index('season_id');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['season_id']);
            $table->dropIndex(['season_id']);
            $table->dropColumn('season_id');
        });
    }
};
