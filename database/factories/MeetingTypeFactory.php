<?php

namespace Database\Factories;

use App\Models\MeetingType;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingTypeFactory extends Factory
{
    protected $model = MeetingType::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'organization_id' => Organization::factory(),
            'name' => $name,
            'slug' => str()->slug($name),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
