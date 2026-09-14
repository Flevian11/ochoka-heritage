<?php

namespace Database\Factories;

use App\Models\FinancialFund;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialFundFactory extends Factory
{
    protected $model = FinancialFund::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        return [
            'organization_id' => Organization::factory(),
            'name' => ucwords($name),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
