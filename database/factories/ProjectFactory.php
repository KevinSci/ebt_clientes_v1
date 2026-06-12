<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['active', 'active', 'paused', 'completed']);

        $progressMap = [
            'active'    => fake()->numberBetween(10, 85),
            'paused'    => fake()->numberBetween(15, 70),
            'completed' => 100,
        ];

        return [
            'company_id'          => Company::factory(),
            'name'                => 'Proyecto ' . fake()->bs(),
            'status'              => $status,
            'progress_percentage' => $progressMap[$status],
        ];
    }

    /**
     * State: active project.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'              => 'active',
            'progress_percentage' => fake()->numberBetween(10, 85),
        ]);
    }

    /**
     * State: completed project.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'              => 'completed',
            'progress_percentage' => 100,
        ]);
    }
}
