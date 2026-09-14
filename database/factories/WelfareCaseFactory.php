<?php

namespace Database\Factories;

use App\Models\WelfareCase;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WelfareCaseFactory extends Factory
{
    protected $model = WelfareCase::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => 'Bereavement Support',
            'slug' => fake()->unique()->slug(),
            'type' => WelfareCase::TYPE_BEREAVEMENT,
            'description' => fake()->sentence(),
            'beneficiary_name' => 'Madam Ujiu',
            'target_amount' => 10000,
            'proposed_support_amount' => 1000,
            'approved_support_amount' => null,
            'status' => WelfareCase::STATUS_DRAFT,
            'created_by' => User::factory(),
            'approved_by' => null,
            'approved_at' => null,
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addDays(30)->toDateString(),
        ];
    }
}
