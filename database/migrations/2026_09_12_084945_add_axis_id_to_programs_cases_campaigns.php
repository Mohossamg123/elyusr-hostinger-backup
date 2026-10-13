<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('programs') && !Schema::hasColumn('programs', 'axis_id')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->foreignId('axis_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('axes')
                    ->nullOnDelete();
                $table->index('axis_id');
            });
        }

        if (Schema::hasTable('case_models') && !Schema::hasColumn('case_models', 'axis_id')) {
            Schema::table('case_models', function (Blueprint $table) {
                $table->foreignId('axis_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('axes')
                    ->nullOnDelete();
                $table->index('axis_id');
            });
        }

        if (Schema::hasTable('volunteer_campaigns') && !Schema::hasColumn('volunteer_campaigns', 'axis_id')) {
            Schema::table('volunteer_campaigns', function (Blueprint $table) {
                $table->foreignId('axis_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('axes')
                    ->nullOnDelete();
                $table->index('axis_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('programs') && Schema::hasColumn('programs', 'axis_id')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropForeign(['axis_id']);
                $table->dropIndex(['axis_id']);
                $table->dropColumn('axis_id');
            });
        }

        if (Schema::hasTable('case_models') && Schema::hasColumn('case_models', 'axis_id')) {
            Schema::table('case_models', function (Blueprint $table) {
                $table->dropForeign(['axis_id']);
                $table->dropIndex(['axis_id']);
                $table->dropColumn('axis_id');
            });
        }

        if (Schema::hasTable('volunteer_campaigns') && Schema::hasColumn('volunteer_campaigns', 'axis_id')) {
            Schema::table('volunteer_campaigns', function (Blueprint $table) {
                $table->dropForeign(['axis_id']);
                $table->dropIndex(['axis_id']);
                $table->dropColumn('axis_id');
            });
        }
    }
};
