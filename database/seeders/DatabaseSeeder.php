<?php

namespace Database\Seeders;

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
        // 1. Crear la cuenta del Administrador
        User::create([
            'name'              => 'Administrador EBT',
            'email'             => 'admin@ebt.com',
            'password'          => Hash::make('Tu_Contraseña_Super_Segura_Aquí'),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
            // 'role'           => 'admin', // Descomenta si usas este campo
        ]);

        // Arreglos de datos fijos para simular contenido real y descriptivo
        $clientNames = ['Corporativo Alfa', 'Logística Silverado', 'Servicios Industriales Delta', 'Consultoría EBT Premium', 'Industrias Omega'];
        $projectNames = ['Auditoría de Sistemas', 'Optimización de Procesos', 'Despliegue de Infraestructura', 'Portal de Clientes v1', 'Gestión de Activos'];
        $postTitles = ['Actualización de avance semanal', 'Revisión de requerimientos iniciales', 'Minuta de la reunión técnica', 'Entrega de entregable fase 1', 'Reporte de incidencias corregidas', 'Cierre de hitos del mes'];

        // 2. Crear 5 usuarios clientes
        foreach ($clientNames as $index => $name) {
            $client = User::create([
                'name'              => $name,
                'email'             => 'cliente' . ($index + 1) . '@ebt.com',
                'password'          => Hash::make('ClientePassword123!'),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
                // 'role'           => 'client', // Descomenta si usas este campo
            ]);

            // Determinar cuántos proyectos tendrá este cliente (entre 2 y 3)
            $totalProjects = rand(2, 3);

            for ($p = 1; $p <= $totalProjects; $p++) {
                // Selecciona un nombre de proyecto aleatorio o secuencial
                $projectName = $projectNames[array_rand($projectNames)] . ' (' . $client->name . ' - ' . $p . ')';

                $project = Project::create([
                    'user_id'     => $client->id, // Relación for($client)
                    'name'       => $projectName,
                    'description' => 'Descripción detallada para el proyecto de ' . $projectName,
                    'status'      => 'active',
                ]);

                // Determinar cuántos posts tendrá este proyecto (entre 4 y 6)
                $totalPosts = rand(4, 6);

                for ($postIndex = 1; $postIndex <= $totalPosts; $postIndex++) {
                    $title = $postTitles[array_rand($postTitles)];

                    Post::create([
                        'project_id' => $project->id, // Relación for($project)
                        'title'      => $title . ' #' . $postIndex,
                        'content'    => 'Este es el cuerpo del post automatizado para el seguimiento del proyecto. Contiene información relevante sobre las tareas ejecutadas en la plataforma.',
                        'created_at' => now()->subDays(rand(1, 30)), // Fechas realistas escalonadas
                    ]);
                }
            }
        }
    }
}