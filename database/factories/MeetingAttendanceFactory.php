<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingAttendance;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingAttendanceFactory extends Factory
{
    protected $model = MeetingAttendance::class;

    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'member_id' => Member::factory(),
            'status' => 'present',
            'recorded_at' => now(),
            'recorded_by' => null,
            'notes' => null,
        ];
    }
}
