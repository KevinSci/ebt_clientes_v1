<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create the administrator account
        // User::factory()->admin()->create([
        //     'name'  => 'Administrador EBT',
        //     'email' => 'admin@ebt.com',
        // ]);

        // // Create 5 client users, each with 2-3 projects and 4-6 posts per project
        // User::factory()->client()->count(5)->create()->each(function (User $client) {
        //     Project::factory()
        //         ->count(fake()->numberBetween(2, 3))
        //         ->for($client)
        //         ->create()
        //         ->each(function (Project $project) {
        //             Post::factory()
        //                 ->count(fake()->numberBetween(4, 6))
        //                 ->for($project)
        //                 ->create();
        //         });
        // });
        User::create([
            'name'              => 'Administrador EBT',
            'email'             => 'admin@ebt.com',
            'password'          => \Illuminate\Support\Facades\Hash::make('Tu_Contraseña_Super_Segura_Aquí'),
            'email_verified_at' => now(),
            // Agrega aquí el campo de rol si tu factory "admin()" alteraba alguna columna:
            // 'role' => 'admin', 
        ]);
    }
}
