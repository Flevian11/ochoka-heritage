<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\SpecialContribution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialContributionFactory extends Factory
{
    protected $model = SpecialContribution::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'target_amount' => 10000,
            'amount_per_member' => 1000,
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addMonth()->toDateString(),
            'status' => SpecialContribution::STATUS_DRAFT,
            'created_by' => User::factory(),
        ];
    }
}
