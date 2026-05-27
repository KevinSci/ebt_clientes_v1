<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear la cuenta del Administrador principal
        User::create([
            'name'              => 'Administrador EBT',
            'email'             => 'admin@ebt.com',
            'password'          => Hash::make('admin'),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ]);

        // Datos maestros simulados para producción limpia en español
        $clientNames = ['Corporativo Alfa', 'Logística Silverado', 'Servicios Industriales Delta', 'Consultoría EBT Premium', 'Industrias Omega'];
        $projectNames = ['Auditoría de Sistemas', 'Optimización de Procesos', 'Despliegue de Infraestructura', 'Portal de Clientes v1', 'Gestión de Activos'];
        $postTitles = ['Actualización de avance semanal', 'Revisión de requerimientos', 'Minuta de reunión técnica', 'Entrega de fase 1', 'Reporte de incidencias'];

        // 2. Crear 5 usuarios clientes
        foreach ($clientNames as $index => $name) {
            $client = User::create([
                'name'              => $name,
                'email'             => 'cliente' . ($index + 1) . '@ebt.com',
                'password'          => Hash::make('ClientePassword123!'),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]);

            // Determinar cuántos proyectos tendrá este cliente (entre 2 y 3)
            $totalProjects = rand(2, 3);

            for ($p = 1; $p <= $totalProjects; $p++) {
                $projectName = $projectNames[array_rand($projectNames)] . ' (' . $p . ')';
                $statuses = ['active', 'paused', 'completed'];

                // CREACIÓN DEL PROYECTO (Alineado con tu migración)
                $project = Project::create([
                    'user_id'             => $client->id,
                    'name'                => $projectName, // Corregido: era 'title'
                    'status'              => $statuses[array_rand($statuses)],
                    'progress_percentage' => rand(10, 100), // Rellena el campo progress_percentage
                ]);

                // Determinar cuántos posts tendrá este proyecto (entre 4 y 6)
                $totalPosts = rand(4, 6);

                for ($postIndex = 1; $postIndex <= $totalPosts; $postIndex++) {
                    $title = $postTitles[array_rand($postTitles)] . ' #' . $postIndex;

                    // CREACIÓN DEL POST (Alineado con tu migración)
                    $post = Post::create([
                        'project_id'   => $project->id,
                        'title'        => $title,
                        'description'  => 'Este es el detalle explicativo del post con la información técnica y de control para la gestión del proyecto en la plataforma.', // Corregido: era 'content'
                        'published_at' => now()->subDays(rand(1, 20)),
                        'created_at'   => now()->subDays(rand(1, 20)),
                    ]);

                }
            }
        }
    }
}