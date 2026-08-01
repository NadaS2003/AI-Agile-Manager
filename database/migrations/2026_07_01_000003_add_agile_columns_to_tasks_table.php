<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sprint_id')->nullable()->after('project_id')->constrained()->onDelete('set null');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->after('status');
            $table->unsignedTinyInteger('story_points')->nullable()->after('priority');
            $table->unsignedInteger('sort_order')->default(0)->after('story_points');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['sprint_id']);
            $table->dropColumn(['project_id', 'sprint_id', 'priority', 'story_points', 'sort_order']);
        });
    }
};
