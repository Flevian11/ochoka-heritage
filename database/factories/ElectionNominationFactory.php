<?php

namespace Database\Factories;

use App\Models\ElectionNomination;
use App\Models\ElectionPosition;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElectionNominationFactory extends Factory
{
    protected $model = ElectionNomination::class;

    public function definition(): array
    {
        return [
            'election_position_id' => ElectionPosition::factory(),
            'candidate_member_id' => Member::factory(),
            'nominated_by' => null,
            'status' => ElectionNomination::PENDING,
            'statement' => fake()->sentence(),
            'eligibility_snapshot' => ['membership_status' => 'active'],
            'verified_by' => null,
            'verified_at' => null,
            'rejection_reason' => null,
        ];
    }
}