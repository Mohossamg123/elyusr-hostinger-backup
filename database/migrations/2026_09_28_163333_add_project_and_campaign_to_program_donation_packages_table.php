<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_donation_packages', function (Blueprint $table) {

            // الباقة لم تعد مجبرة أن تكون تابعة لبرنامج
            $table->foreignId('program_id')
                ->nullable()
                ->change();

            // باقات المشاريع
            $table->foreignId('project_id')
                ->nullable()
                ->after('program_id')
                ->constrained('projects')
                ->nullOnDelete();

            // باقات حملات التبرع
            $table->foreignId('donation_campaign_id')
                ->nullable()
                ->after('project_id')
                ->constrained('donation_campaigns')
                ->nullOnDelete();

            $table->index(['project_id', 'is_active']);
            $table->index(['donation_campaign_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('program_donation_packages', function (Blueprint $table) {

            $table->dropForeign(['project_id']);
            $table->dropForeign(['donation_campaign_id']);

            $table->dropIndex([
                'program_donation_packages_project_id_is_active_index'
            ]);

            $table->dropIndex([
                'program_donation_packages_donation_campaign_id_is_active_index'
            ]);

            $table->dropColumn([
                'project_id',
                'donation_campaign_id',
            ]);

            $table->foreignId('program_id')
                ->nullable(false)
                ->change();
        });
    }
};