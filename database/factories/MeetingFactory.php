<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingType;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    public function definition(): array
    {
        $start = now()->addDays(fake()->numberBetween(1, 30))->setTime(fake()->numberBetween(8, 18), 0);

        return [
            'organization_id' => Organization::factory(),
            'meeting_type_id' => null,
            'organizer_id' => User::factory(),
            'title' => fake()->sentence(5),
            'description' => fake()->optional()->paragraph(),
            'starts_at' => $start,
            'ends_at' => (clone $start)->addHours(2),
            'location' => fake()->optional()->address(),
            'format' => Meeting::FORMAT_PHYSICAL,
            'google_meet_url' => null,
            'agenda' => fake()->optional()->paragraph(),
            'status' => Meeting::STATUS_SCHEDULED,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Meeting $meeting): void {
            if ($meeting->meeting_type_id === null) {
                $meeting->meeting_type_id = MeetingType::factory()
                    ->create(['organization_id' => $meeting->organization_id])
                    ->id;
            }
        });
    }
}
