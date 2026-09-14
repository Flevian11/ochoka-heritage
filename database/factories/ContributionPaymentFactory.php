<?php

namespace Database\Factories;

use App\Models\ContributionPayment;
use App\Models\Member;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContributionPayment> */
class ContributionPaymentFactory extends Factory
{
    protected $model = ContributionPayment::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'member_id' => Member::factory(),
            'amount' => 200,
            'received_at' => now(),
            'method' => ContributionPayment::METHOD_CASH,
            'reference' => 'PAY-' . fake()->unique()->numerify('######'),
            'status' => ContributionPayment::STATUS_VERIFIED,
            'notes' => null,
        ];
    }
}
