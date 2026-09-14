<?php

namespace Database\Factories;

use App\Models\ExecutivePosition;
use App\Models\Member;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExecutiveAppointmentFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'organization_id' => Organization::factory(),
            'member_id' => Member::factory(),
            'executive_position_id' => ExecutivePosition::factory(),
            'starts_at' => $startsAt->format('Y-m-d'),
            'ends_at' => null,
            'status' => 'active',
            'appointed_by' => null,
            'appointed_at' => now(),
            'revoked_at' => null,
            'revoked_by' => null,
            'reason' => null,
        ];
    }
}
