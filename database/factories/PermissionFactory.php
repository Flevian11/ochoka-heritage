<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    public function definition(): array
    {
        $key = fake()->unique()->slug(3, '.');

        return [
            'key' => $key,
            'name' => fake()->words(3, true),
            'group' => fake()->word(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
