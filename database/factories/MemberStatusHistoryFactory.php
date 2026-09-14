<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MemberStatusHistory;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MemberStatusHistory> */
class MemberStatusHistoryFactory extends Factory
{
    protected $model = MemberStatusHistory::class;

    public function definition(): array
    {
        $from = Member::STATUS_ACTIVE;
        return [
            'organization_id' => Organization::factory(),
            'member_id' => Member::factory(),
            'from_status' => $from,
            'to_status' => Member::STATUS_SUSPENDED,
            'reason' => 'Administrative status change.',
            'changed_by' => null,
            'changed_at' => now(),
        ];
    }
}
