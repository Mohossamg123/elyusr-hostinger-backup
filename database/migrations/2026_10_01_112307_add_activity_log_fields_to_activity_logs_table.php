<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('entity_type')->nullable()->after('action');
            $table->unsignedBigInteger('entity_id')->nullable()->after('entity_type');
            $table->string('role')->nullable()->after('entity_id');
            $table->text('summary')->nullable()->after('role');
            $table->json('metadata')->nullable()->after('summary');

            $table->index('entity_type');
            $table->index('entity_id');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['entity_type']);
            $table->dropIndex(['entity_id']);
            $table->dropIndex(['role']);

            $table->dropColumn([
                'entity_type',
                'entity_id',
                'role',
                'summary',
                'metadata',
            ]);
        });
    }
};