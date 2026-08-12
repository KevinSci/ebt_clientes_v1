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
        // 1. Add progress_mode to projects table
        if (!Schema::hasColumn('projects', 'progress_mode')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->enum('progress_mode', ['manual', 'phases'])->default('manual')->after('status');
            });
        }

        // 2. Create project_phases table
        if (!Schema::hasTable('project_phases')) {
            Schema::create('project_phases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
                $table->softDeletes();
                $table->timestamps();
                $table->index(['project_id', 'sort_order']);
            });
        }

        // 3. Create project_tasks table
        if (!Schema::hasTable('project_tasks')) {
            Schema::create('project_tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('phase_id')->constrained('project_phases')->cascadeOnDelete();
                $table->string('name');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
                $table->index(['phase_id', 'is_completed']);
            });
        }

        // 4. Add project_task_id to posts table
        if (!Schema::hasColumn('posts', 'project_task_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->foreignId('project_task_id')->nullable()->after('project_id')
                      ->constrained('project_tasks')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('posts', 'project_task_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropForeign(['project_task_id']);
                $table->dropColumn('project_task_id');
            });
        }

        Schema::dropIfExists('project_tasks');
        Schema::dropIfExists('project_phases');

        if (Schema::hasColumn('projects', 'progress_mode')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('progress_mode');
            });
        }
    }
};
