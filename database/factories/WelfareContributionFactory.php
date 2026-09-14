<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Organization;
use App\Models\WelfareCase;
use App\Models\WelfareContribution;
use Illuminate\Database\Eloquent\Factories\Factory;

class WelfareContributionFactory extends Factory
{
    protected $model = WelfareContribution::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'welfare_case_id' => WelfareCase::factory(),
            'member_id' => Member::factory(),
            'amount' => 200,
            'source_type' => WelfareContribution::SOURCE_INDEPENDENT_PAYMENT,
            'payment_method' => 'cash',
            'reference' => fake()->unique()->bothify('WEL-####'),
            'paid_on' => now()->toDateString(),
            'status' => WelfareContribution::STATUS_PENDING,
        ];
    }
}
