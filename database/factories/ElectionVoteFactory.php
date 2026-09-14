<?php

namespace Database\Factories;

use App\Models\ElectionBallot;
use App\Models\ElectionPosition;
use App\Models\ElectionVote;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElectionVoteFactory extends Factory
{
    protected $model = ElectionVote::class;

    public function definition(): array
    {
        return [
            'ballot_id' => ElectionBallot::factory(),
            'election_position_id' => ElectionPosition::factory(),
            'candidate_member_id' => Member::factory(),
            'selection_order' => null,
        ];
    }
}