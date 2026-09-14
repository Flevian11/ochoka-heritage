<?php

namespace Database\Factories;

use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\ExecutivePosition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElectionPositionFactory extends Factory
{
    protected $model = ElectionPosition::class;

    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'executive_position_id' => ExecutivePosition::factory(),
            'seats' => 1,
            'max_votes_per_voter' => 1,
            'nomination_limit' => null,
            'eligibility_criteria' => null,
        ];
    }
}