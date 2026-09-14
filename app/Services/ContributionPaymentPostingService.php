<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ContributionPayment;
use App\Models\FinancialFund;
use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ContributionPaymentPostingService
{
    public function verify(ContributionPayment $payment, User $actor, ?string $reason = null): ContributionPayment
    {
        return DB::transaction(function () use ($payment, $actor, $reason) {
            $payment = ContributionPayment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($payment->status === ContributionPayment::STATUS_REVERSED) {
                throw new RuntimeException('A reversed payment cannot be verified.');
            }

            if ($payment->status === ContributionPayment::STATUS_VERIFIED) {
                return $payment->load('ledgerTransaction');
            }

            $before = $payment->only(['status', 'verified_by', 'verified_at']);

            $payment->forceFill([
                'status' => ContributionPayment::STATUS_VERIFIED,
                'verified_by' => $actor->id,
                'verified_at' => now(),
            ])->save();

            $this->audit($payment, $actor, 'verified', $before, $payment->only(['status', 'verified_by', 'verified_at']), $reason);

            return $payment->fresh();
        });
    }

    public function postToLedger(ContributionPayment $payment, User $actor, FinancialFund $fund): FinancialTransaction
    {
        return DB::transaction(function () use ($payment, $actor, $fund) {
            $payment = ContributionPayment::query()->lockForUpdate()->findOrFail($payment->id);
            $fund = FinancialFund::query()->lockForUpdate()->findOrFail($fund->id);

            if ($payment->organization_id !== $fund->organization_id) {
                throw new RuntimeException('The payment and financial fund must belong to the same organization.');
            }

            if ($payment->status !== ContributionPayment::STATUS_VERIFIED) {
                throw new RuntimeException('Only verified payments can be posted to the ledger.');
            }

            $existing = FinancialTransaction::query()
                ->where('source_type', $payment->getMorphClass())
                ->where('source_id', $payment->id)
                ->first();

            if ($existing) {
                if ($existing->status === FinancialTransaction::STATUS_REVERSED) {
                    throw new RuntimeException('A reversed ledger transaction cannot be posted again from the same payment.');
                }

                return $existing;
            }

            $transaction = FinancialTransaction::create([
                'organization_id' => $payment->organization_id,
                'financial_fund_id' => $fund->id,
                'member_id' => $payment->member_id,
                'recorded_by' => $actor->id,
                'transaction_number' => $this->transactionNumber(),
                'transaction_type' => FinancialTransaction::TYPE_CONTRIBUTION,
                'direction' => FinancialTransaction::DIRECTION_IN,
                'amount' => $payment->amount,
                'transaction_date' => $payment->received_at->toDateString(),
                'reference' => $payment->reference,
                'description' => 'Contribution payment ' . $payment->id,
                'source_type' => $payment->getMorphClass(),
                'source_id' => $payment->id,
                'status' => FinancialTransaction::STATUS_POSTED,
                'posted_at' => now(),
            ]);

            $this->audit($payment, $actor, 'ledger_posted', null, [
                'financial_transaction_id' => $transaction->id,
                'financial_fund_id' => $fund->id,
                'amount' => (string) $payment->amount,
            ], null, $transaction->transaction_number);

            return $transaction;
        });
    }

    public function reverse(ContributionPayment $payment, User $actor, string $reason): ContributionPayment
    {
        return DB::transaction(function () use ($payment, $actor, $reason) {
            $payment = ContributionPayment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($payment->status === ContributionPayment::STATUS_REVERSED) {
                return $payment->fresh();
            }

            $before = $payment->only(['status', 'reversed_by', 'reversed_at', 'reversal_reason']);

            $payment->forceFill([
                'status' => ContributionPayment::STATUS_REVERSED,
                'reversed_by' => $actor->id,
                'reversed_at' => now(),
                'reversal_reason' => $reason,
            ])->save();

            $transaction = FinancialTransaction::query()
                ->where('source_type', $payment->getMorphClass())
                ->where('source_id', $payment->id)
                ->first();

            if ($transaction && $transaction->status !== FinancialTransaction::STATUS_REVERSED) {
                $transaction->update([
                    'status' => FinancialTransaction::STATUS_REVERSED,
                    'reversed_by' => $actor->id,
                    'reversed_at' => now(),
                    'reversal_reason' => $reason,
                ]);
            }

            $this->audit($payment, $actor, 'reversed', $before, $payment->only(['status', 'reversed_by', 'reversed_at', 'reversal_reason']), $reason, $payment->reference);

            return $payment->fresh();
        });
    }

    private function audit(ContributionPayment $payment, User $actor, string $action, ?array $previous, ?array $new, ?string $reason, ?string $reference = null): void
    {
        AuditLog::create([
            'organization_id' => $payment->organization_id,
            'user_id' => $actor->id,
            'role_name' => $actor->activeRoles()->value('name'),
            'action' => $action,
            'module' => 'finance',
            'auditable_type' => $payment->getMorphClass(),
            'auditable_id' => $payment->id,
            'previous_values' => $previous,
            'new_values' => $new,
            'reason' => $reason,
            'reference' => $reference,
        ]);
    }

    private function transactionNumber(): string
    {
        do {
            $number = 'CT-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
        } while (FinancialTransaction::query()->where('transaction_number', $number)->exists());

        return $number;
    }
}
