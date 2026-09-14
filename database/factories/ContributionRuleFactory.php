<?php

namespace Database\Factories;

use App\Models\ContributionRule;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContributionRule> */
class ContributionRuleFactory extends Factory
{
    protected $model = ContributionRule::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => 'Monthly Contribution',
            'description' => null,
            'amount' => 200,
            'frequency' => ContributionRule::FREQUENCY_MONTHLY,
            'effective_from' => now()->startOfYear()->toDateString(),
            'effective_to' => null,
            'is_active' => true,
        ];
    }
}
