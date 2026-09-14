<?php

namespace Database\Factories;

use App\Models\Election;
use App\Models\ElectionVoterEligibility;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElectionVoterEligibilityFactory extends Factory
{
    protected $model = ElectionVoterEligibility::class;

    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'member_id' => Member::factory(),
            'eligible' => true,
            'criteria_snapshot' => ['membership_status' => 'active'],
            'snapshotted_at' => now(),
        ];
    }
}