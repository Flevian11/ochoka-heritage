<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingParticipantFactory extends Factory
{
    protected $model = MeetingParticipant::class;

    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'member_id' => Member::factory(),
            'role' => fake()->optional()->jobTitle(),
            'is_required' => fake()->boolean(),
        ];
    }
}
