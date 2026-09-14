<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingDecision;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingDecisionFactory extends Factory
{
    protected $model = MeetingDecision::class;

    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'sequence' => 1,
            'description' => fake()->sentence(),
            'resolution' => fake()->optional()->sentence(),
        ];
    }
}
