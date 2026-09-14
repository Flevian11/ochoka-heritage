<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\WelfareCase;
use App\Models\WelfareSupportObligation;
use Illuminate\Database\Eloquent\Factories\Factory;

class WelfareSupportObligationFactory extends Factory
{
    protected $model = WelfareSupportObligation::class;

    public function definition(): array
    {
        return [
            'welfare_case_id' => WelfareCase::factory(),
            'member_id' => Member::factory(),
            'amount' => 1000,
            'paid_amount' => 0,
            'status' => WelfareSupportObligation::STATUS_PENDING,
        ];
    }
}
