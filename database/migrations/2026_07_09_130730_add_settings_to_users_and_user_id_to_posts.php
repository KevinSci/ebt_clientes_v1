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
        if (!Schema::hasColumn('users', 'settings')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('settings')->nullable()->after('phone');
            });
        }

        if (!Schema::hasColumn('posts', 'user_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('posts', 'user_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasColumn('users', 'settings')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('settings');
            });
        }
    }
};
