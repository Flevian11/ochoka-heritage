<?php

namespace Tests\Feature;

use App\Models\FinancialExpenditure;
use App\Models\FinancialFund;
use App\Models\Member;
use App\Models\Organization;
use App\Models\SpecialContribution;
use App\Models\SpecialContributionObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpecialContributionExpenditureFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_special_contribution_can_define_member_obligation_without_treating_it_as_monthly_contribution(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->create(['organization_id' => $organization->id]);
        $special = SpecialContribution::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Family Support Drive',
        ]);

        $obligation = SpecialContributionObligation::factory()->create([
            'special_contribution_id' => $special->id,
            'member_id' => $member->id,
            'amount' => 1000,
            'paid_amount' => 0,
            'status' => SpecialContributionObligation::STATUS_PENDING,
        ]);

        $this->assertSame(1000.0, $obligation->outstandingAmount());
        $this->assertSame(SpecialContribution::STATUS_DRAFT, $special->status);
    }

    public function test_special_contribution_obligation_supports_partial_and_paid_states(): void
    {
        $obligation = SpecialContributionObligation::factory()->create([
            'amount' => 1000,
            'paid_amount' => 400,
            'status' => SpecialContributionObligation::STATUS_PARTIAL,
        ]);

        $this->assertSame(600.0, $obligation->outstandingAmount());

        $obligation->update([
            'paid_amount' => 1000,
            'status' => SpecialContributionObligation::STATUS_PAID,
        ]);

        $this->assertSame(0.0, $obligation->outstandingAmount());
    }

    public function test_expenditure_is_separate_from_ledger_and_starts_as_draft(): void
    {
        $organization = Organization::factory()->create();
        $fund = FinancialFund::factory()->create(['organization_id' => $organization->id]);
        $user = User::factory()->create();

        $expenditure = FinancialExpenditure::factory()->create([
            'organization_id' => $organization->id,
            'financial_fund_id' => $fund->id,
            'recorded_by' => $user->id,
            'amount' => 2500,
        ]);

        $this->assertSame(FinancialExpenditure::STATUS_DRAFT, $expenditure->status);
        $this->assertNull($expenditure->ledger_transaction_id);
        $this->assertSame(2500.0, (float) $expenditure->amount);
    }

    public function test_expenditure_can_be_reversed_without_deleting_source_record(): void
    {
        $expenditure = FinancialExpenditure::factory()->create([
            'status' => FinancialExpenditure::STATUS_PAID,
        ]);

        $expenditure->update([
            'status' => FinancialExpenditure::STATUS_REVERSED,
            'reversed_at' => now(),
            'reversal_reason' => 'Duplicate payment',
        ]);

        $expenditure->refresh();

        $this->assertSame(FinancialExpenditure::STATUS_REVERSED, $expenditure->status);
        $this->assertNotNull($expenditure->reversed_at);
        $this->assertSame('Duplicate payment', $expenditure->reversal_reason);
        $this->assertDatabaseHas('financial_expenditures', ['id' => $expenditure->id]);
    }
}
