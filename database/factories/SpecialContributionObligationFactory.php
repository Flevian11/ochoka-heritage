<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\SpecialContribution;
use App\Models\SpecialContributionObligation;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialContributionObligationFactory extends Factory
{
    protected $model = SpecialContributionObligation::class;

    public function definition(): array
    {
        return [
            'special_contribution_id' => SpecialContribution::factory(),
            'member_id' => Member::factory(),
            'amount' => 1000,
            'paid_amount' => 0,
            'status' => SpecialContributionObligation::STATUS_PENDING,
        ];
    }
}
