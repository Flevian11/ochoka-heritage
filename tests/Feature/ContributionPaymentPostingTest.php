<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\ContributionPayment;
use App\Models\FinancialFund;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use App\Services\ContributionPaymentPostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ContributionPaymentPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_payment_cannot_be_posted_until_verified(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->for($organization)->create();
        $actor = User::factory()->create();
        $fund = FinancialFund::factory()->for($organization)->create();
        $payment = ContributionPayment::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'status' => ContributionPayment::STATUS_PENDING,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Only verified payments can be posted to the ledger.');

        app(ContributionPaymentPostingService::class)->postToLedger($payment, $actor, $fund);

        $this->assertDatabaseCount('financial_transactions', 0);
    }

    public function test_verification_records_actor_and_audit_trail(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->for($organization)->create();
        $actor = User::factory()->create();
        $payment = ContributionPayment::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'status' => ContributionPayment::STATUS_PENDING,
        ]);

        $verified = app(ContributionPaymentPostingService::class)->verify($payment, $actor, 'Cash receipt checked');

        $this->assertSame(ContributionPayment::STATUS_VERIFIED, $verified->status);
        $this->assertSame($actor->id, $verified->verified_by);
        $this->assertNotNull($verified->verified_at);
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => ContributionPayment::class,
            'auditable_id' => $payment->id,
            'action' => 'verified',
            'module' => 'finance',
            'user_id' => $actor->id,
        ]);
    }

    public function test_verified_payment_posts_once_to_ledger_and_keeps_payment_separate(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->for($organization)->create();
        $actor = User::factory()->create();
        $fund = FinancialFund::factory()->for($organization)->create();
        $payment = ContributionPayment::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'amount' => 1000,
            'status' => ContributionPayment::STATUS_PENDING,
            'reference' => 'CASH-1000',
        ]);

        $service = app(ContributionPaymentPostingService::class);
        $service->verify($payment, $actor);
        $transaction = $service->postToLedger($payment, $actor, $fund);
        $sameTransaction = $service->postToLedger($payment, $actor, $fund);

        $this->assertSame($transaction->id, $sameTransaction->id);
        $this->assertDatabaseCount('financial_transactions', 1);
        $this->assertDatabaseHas('financial_transactions', [
            'id' => $transaction->id,
            'financial_fund_id' => $fund->id,
            'member_id' => $member->id,
            'transaction_type' => FinancialTransaction::TYPE_CONTRIBUTION,
            'direction' => FinancialTransaction::DIRECTION_IN,
            'amount' => 1000.00,
            'source_type' => ContributionPayment::class,
            'source_id' => $payment->id,
            'status' => FinancialTransaction::STATUS_POSTED,
        ]);
        $this->assertSame($transaction->id, $payment->fresh()->ledgerTransaction->id);
    }

    public function test_reversal_marks_both_payment_and_ledger_reversed_without_deleting_history(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->for($organization)->create();
        $actor = User::factory()->create();
        $fund = FinancialFund::factory()->for($organization)->create();
        $payment = ContributionPayment::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'status' => ContributionPayment::STATUS_PENDING,
            'amount' => 200,
        ]);

        $service = app(ContributionPaymentPostingService::class);
        $service->verify($payment, $actor);
        $transaction = $service->postToLedger($payment, $actor, $fund);
        $service->reverse($payment, $actor, 'Duplicate cash receipt');

        $this->assertDatabaseHas('contribution_payments', [
            'id' => $payment->id,
            'status' => ContributionPayment::STATUS_REVERSED,
            'reversed_by' => $actor->id,
            'reversal_reason' => 'Duplicate cash receipt',
        ]);
        $this->assertDatabaseHas('financial_transactions', [
            'id' => $transaction->id,
            'status' => FinancialTransaction::STATUS_REVERSED,
            'reversed_by' => $actor->id,
            'reversal_reason' => 'Duplicate cash receipt',
        ]);
        $this->assertDatabaseCount('financial_transactions', 1);
        $this->assertDatabaseCount('contribution_payments', 1);
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => ContributionPayment::class,
            'auditable_id' => $payment->id,
            'action' => 'reversed',
            'reason' => 'Duplicate cash receipt',
        ]);
    }

    public function test_payment_and_fund_must_belong_to_same_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $member = Member::factory()->for($organization)->create();
        $actor = User::factory()->create();
        $fund = FinancialFund::factory()->for($otherOrganization)->create();
        $payment = ContributionPayment::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'status' => ContributionPayment::STATUS_VERIFIED,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('same organization');

        app(ContributionPaymentPostingService::class)->postToLedger($payment, $actor, $fund);
    }
}
