<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingActionItem;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingActionItemFactory extends Factory
{
    protected $model = MeetingActionItem::class;

    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'responsible_member_id' => Member::factory(),
            'description' => fake()->sentence(),
            'status' => 'open',
            'due_date' => now()->addWeeks(2)->toDateString(),
            'completed_at' => null,
        ];
    }
}
