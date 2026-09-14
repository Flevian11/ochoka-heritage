<?php

namespace Database\Factories;

use App\Models\FinancialFund;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialTransactionFactory extends Factory
{
    protected $model = FinancialTransaction::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'financial_fund_id' => FinancialFund::factory(),
            'member_id' => null,
            'recorded_by' => User::factory(),
            'transaction_number' => 'TXN-' . fake()->unique()->numerify('########'),
            'transaction_type' => FinancialTransaction::TYPE_ADJUSTMENT,
            'direction' => FinancialTransaction::DIRECTION_IN,
            'amount' => fake()->randomFloat(2, 1, 50000),
            'transaction_date' => now()->toDateString(),
            'reference' => fake()->optional()->bothify('REF-####??'),
            'description' => fake()->optional()->sentence(),
            'source_type' => null,
            'source_id' => null,
            'status' => FinancialTransaction::STATUS_POSTED,
            'posted_at' => now(),
            'reversed_by' => null,
            'reversed_at' => null,
            'reversal_reason' => null,
        ];
    }
}
