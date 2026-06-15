<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add company_id as nullable first
        if (!Schema::hasColumn('projects', 'company_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('company_id')->after('id')->nullable()->constrained()->cascadeOnDelete();
            });
        }

        // 2. Run the DataMigrationSeeder to populate companies and company_user
        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\DataMigrationSeeder',
            '--force' => true,
        ]);

        // 3. Map projects.user_id to company_id using company_user pivot
        $projects = DB::table('projects')->get();
        foreach ($projects as $project) {
            // Check if user_id column still exists (it might not if rerun)
            if (isset($project->user_id)) {
                $companyUser = DB::table('company_user')
                    ->where('user_id', $project->user_id)
                    ->first();

                if ($companyUser) {
                    DB::table('projects')
                        ->where('id', $project->id)
                        ->update(['company_id' => $companyUser->company_id]);
                }
            }
        }

        // 4. Drop user_id foreign key constraint and column
        if (Schema::hasColumn('projects', 'user_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        // 5. Change company_id to not nullable
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add user_id column back as nullable
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // 2. Map company_id back to user_id
        $projects = DB::table('projects')->get();
        foreach ($projects as $project) {
            $companyUser = DB::table('company_user')
                ->where('company_id', $project->company_id)
                ->first();

            if ($companyUser) {
                DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['user_id' => $companyUser->user_id]);
            }
        }

        // 3. Drop company_id foreign key and column
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        // 4. Make user_id NOT NULL
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
