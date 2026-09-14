<?php

namespace Database\Factories;

use App\Models\ContributionPayment;
use App\Models\ContributionPaymentAllocation;
use App\Models\MemberContributionObligation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContributionPaymentAllocation> */
class ContributionPaymentAllocationFactory extends Factory
{
    protected $model = ContributionPaymentAllocation::class;

    public function definition(): array
    {
        return [
            'contribution_payment_id' => ContributionPayment::factory(),
            'member_contribution_obligation_id' => MemberContributionObligation::factory(),
            'amount' => 200,
        ];
    }
}
