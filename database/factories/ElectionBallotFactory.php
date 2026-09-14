<?php

namespace Database\Factories;

use App\Models\Election;
use App\Models\ElectionBallot;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ElectionBallotFactory extends Factory
{
    protected $model = ElectionBallot::class;

    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'voter_member_id' => Member::factory(),
            'ballot_token' => (string) Str::uuid(),
            'status' => 'cast',
            'cast_at' => now(),
            'sealed_at' => null,
        ];
    }
}