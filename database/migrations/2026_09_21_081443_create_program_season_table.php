<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_season', function (Blueprint $table) {
            $table->id();

            $table->foreignId('program_id')
                ->constrained('programs')
                ->cascadeOnDelete();

            $table->foreignId('season_id')
                ->constrained('seasons')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['program_id', 'season_id']);
            $table->index(['season_id', 'program_id']);
        });

        // نقل أي روابط موجودة حاليًا من programs.season_id
        if (Schema::hasColumn('programs', 'season_id')) {
            DB::statement("
                INSERT INTO program_season (program_id, season_id, created_at, updated_at)
                SELECT id, season_id, NOW(), NOW()
                FROM programs
                WHERE season_id IS NOT NULL
            ");
        }

        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['season_id']);
            $table->dropIndex(['season_id']);
            $table->dropColumn('season_id');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->foreignId('season_id')
                ->nullable()
                ->after('axis_id')
                ->constrained('seasons')
                ->nullOnDelete();

            $table->index('season_id');
        });

        DB::statement("
            UPDATE programs p
            INNER JOIN (
                SELECT program_id, MIN(season_id) AS season_id
                FROM program_season
                GROUP BY program_id
            ) ps ON ps.program_id = p.id
            SET p.season_id = ps.season_id
        ");

        Schema::dropIfExists('program_season');
    }
};
