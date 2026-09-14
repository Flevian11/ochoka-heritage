<?php

namespace Tests\Feature;

use App\Models\ContributionPayment;
use App\Models\ContributionPaymentAllocation;
use App\Models\ContributionRule;
use App\Models\Member;
use App\Models\MemberContributionObligation;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContributionFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_contribution_rule_is_effective_dated(): void
    {
        $rule = ContributionRule::factory()->create([
            'amount' => 200,
            'effective_from' => '2026-01-01',
            'effective_to' => '2026-12-31',
        ]);

        $this->assertTrue($rule->appliesToPeriod('2026-10-01'));
        $this->assertFalse($rule->appliesToPeriod('2025-12-01'));
        $this->assertFalse($rule->appliesToPeriod('2027-01-01'));
    }

    public function test_payment_does_not_reduce_outstanding_until_allocated(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->create(['organization_id' => $organization->id]);
        $rule = ContributionRule::factory()->create(['organization_id' => $organization->id]);
        $obligation = MemberContributionObligation::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'contribution_rule_id' => $rule->id,
            'period' => '2026-10-01',
            'amount_due' => 200,
        ]);

        $payment = ContributionPayment::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'amount' => 1000,
        ]);

        $this->assertSame(0.0, $payment->allocatedAmount());
        $this->assertSame(1000.0, $payment->unallocatedAmount());
        $this->assertSame(200.0, $obligation->balance());
        $this->assertSame(MemberContributionObligation::STATUS_UNPAID, $obligation->status);
    }

    public function test_one_payment_can_be_allocated_across_months_or_all_to_one_month(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->create(['organization_id' => $organization->id]);
        $rule = ContributionRule::factory()->create(['organization_id' => $organization->id, 'amount' => 200]);

        $months = collect(range(1, 10))->mapWithKeys(function (int $month) use ($organization, $member, $rule) {
            $period = sprintf('2026-%02d-01', $month);
            return [$month => MemberContributionObligation::factory()->create([
                'organization_id' => $organization->id,
                'member_id' => $member->id,
                'contribution_rule_id' => $rule->id,
                'period' => $period,
                'amount_due' => 200,
            ])];
        });

        $payment = ContributionPayment::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'amount' => 1000,
        ]);

        foreach ($months as $obligation) {
            ContributionPaymentAllocation::factory()->create([
                'contribution_payment_id' => $payment->id,
                'member_contribution_obligation_id' => $obligation->id,
                'amount' => 100,
            ]);
        }

        $months->each(fn ($obligation) => $obligation->refreshStatus());

        $this->assertSame(1000.0, $payment->allocatedAmount());
        $this->assertSame(0.0, $payment->unallocatedAmount());
        $this->assertCount(0, $months->filter(fn ($obligation) => $obligation->fresh()->status === MemberContributionObligation::STATUS_PAID));
        $this->assertCount(10, $months->filter(fn ($obligation) => $obligation->fresh()->status === MemberContributionObligation::STATUS_PARTIAL));

        $singleMonthPayment = ContributionPayment::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'amount' => 1000,
        ]);
        $october = $months->get(10);

        ContributionPaymentAllocation::factory()->create([
            'contribution_payment_id' => $singleMonthPayment->id,
            'member_contribution_obligation_id' => $october->id,
            'amount' => 1000,
        ]);
        $october->refreshStatus();

        $this->assertSame(1000.0, $singleMonthPayment->allocatedAmount());
        $this->assertSame(MemberContributionObligation::STATUS_PAID, $october->fresh()->status);
        $this->assertSame(0.0, $october->fresh()->balance());
    }
}
