<?php

namespace Database\Factories;

use App\Models\FinancialExpenditure;
use App\Models\FinancialFund;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialExpenditureFactory extends Factory
{
    protected $model = FinancialExpenditure::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'financial_fund_id' => FinancialFund::factory(),
            'category' => 'General',
            'description' => fake()->sentence(),
            'amount' => 500,
            'spent_on' => now()->toDateString(),
            'payee' => fake()->name(),
            'payment_method' => 'cash',
            'reference' => fake()->unique()->bothify('EXP-####'),
            'status' => FinancialExpenditure::STATUS_DRAFT,
            'recorded_by' => User::factory(),
        ];
    }
}
