<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {

            $table->foreignId('project_id')
                ->nullable()
                ->after('program_id')
                ->constrained('projects')
                ->nullOnDelete();

            $table->foreignId('donation_campaign_id')
                ->nullable()
                ->after('project_id')
                ->constrained('donation_campaigns')
                ->nullOnDelete();

            $table->index([
                'program_id',
                'project_id',
                'donation_campaign_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {

            $table->dropForeign(['donation_campaign_id']);
            $table->dropForeign(['project_id']);

            $table->dropIndex([
                'program_id',
                'project_id',
                'donation_campaign_id',
            ]);

            $table->dropColumn([
                'project_id',
                'donation_campaign_id',
            ]);
        });
    }
};