<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->company(),
            'rfc'        => fake()->regexify('[A-Z]{4}[0-9]{6}[A-Z0-9]{3}'),
            'address'    => fake()->address(),
            'phone'      => fake()->phoneNumber(),
            'tax_regime' => fake()->randomElement(['fisica', 'moral']),
        ];
    }
}
