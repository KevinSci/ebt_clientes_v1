<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id'   => Project::factory(),
            'title'        => fake()->sentence(fake()->numberBetween(4, 8)),
            'description'  => fake()->paragraphs(fake()->numberBetween(2, 5), true),
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
