<?php

namespace Database\Factories;

use App\Models\ContributionRule;
use App\Models\Member;
use App\Models\MemberContributionObligation;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MemberContributionObligation> */
class MemberContributionObligationFactory extends Factory
{
    protected $model = MemberContributionObligation::class;

    public function definition(): array
    {
        $member = Member::factory();
        $rule = ContributionRule::factory();

        return [
            'organization_id' => Organization::factory(),
            'member_id' => $member,
            'contribution_rule_id' => $rule,
            'period' => now()->startOfMonth()->toDateString(),
            'amount_due' => 200,
            'status' => MemberContributionObligation::STATUS_UNPAID,
            'notes' => null,
        ];
    }
}
