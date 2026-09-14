<?php

namespace Tests\Feature;

use App\Models\FinancialFund;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialLedgerFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_fund_and_ledger_transaction_record_financial_context(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->for($organization)->create();
        $user = User::factory()->create();
        $fund = FinancialFund::factory()->for($organization)->create([
            'name' => 'General Contribution Fund',
            'slug' => 'general-contribution',
        ]);

        $transaction = FinancialTransaction::factory()->create([
            'organization_id' => $organization->id,
            'financial_fund_id' => $fund->id,
            'member_id' => $member->id,
            'recorded_by' => $user->id,
            'transaction_type' => FinancialTransaction::TYPE_CONTRIBUTION,
            'direction' => FinancialTransaction::DIRECTION_IN,
            'amount' => 1000,
            'reference' => 'MPESA-EXAMPLE',
        ]);

        $this->assertDatabaseHas('financial_transactions', [
            'id' => $transaction->id,
            'organization_id' => $organization->id,
            'financial_fund_id' => $fund->id,
            'member_id' => $member->id,
            'transaction_type' => 'contribution',
            'direction' => 'in',
            'amount' => 1000.00,
        ]);

        $this->assertTrue($transaction->isIncome());
        $this->assertFalse($transaction->isExpense());
    }

    public function test_ledger_supports_income_and_expenditure_without_reusing_payment_as_ledger_record(): void
    {
        $organization = Organization::factory()->create();
        $fund = FinancialFund::factory()->for($organization)->create();

        $income = FinancialTransaction::factory()->create([
            'organization_id' => $organization->id,
            'financial_fund_id' => $fund->id,
            'transaction_type' => FinancialTransaction::TYPE_SPECIAL_CONTRIBUTION,
            'direction' => FinancialTransaction::DIRECTION_IN,
            'amount' => 5000,
        ]);

        $expense = FinancialTransaction::factory()->create([
            'organization_id' => $organization->id,
            'financial_fund_id' => $fund->id,
            'transaction_type' => FinancialTransaction::TYPE_EXPENDITURE,
            'direction' => FinancialTransaction::DIRECTION_OUT,
            'amount' => 1500,
        ]);

        $this->assertTrue($income->isIncome());
        $this->assertTrue($expense->isExpense());
        $this->assertDatabaseCount('financial_transactions', 2);
    }

    public function test_reversal_preserves_original_transaction_history(): void
    {
        $organization = Organization::factory()->create();
        $fund = FinancialFund::factory()->for($organization)->create();
        $user = User::factory()->create();

        $transaction = FinancialTransaction::factory()->create([
            'organization_id' => $organization->id,
            'financial_fund_id' => $fund->id,
            'recorded_by' => $user->id,
            'amount' => 200,
        ]);

        $transaction->update([
            'status' => FinancialTransaction::STATUS_REVERSED,
            'reversed_by' => $user->id,
            'reversed_at' => now(),
            'reversal_reason' => 'Recorded in error',
        ]);

        $this->assertDatabaseHas('financial_transactions', [
            'id' => $transaction->id,
            'amount' => 200.00,
            'status' => 'reversed',
            'reversal_reason' => 'Recorded in error',
        ]);
    }
}
