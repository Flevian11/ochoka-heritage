<?php

namespace Database\Factories;

use App\Models\MembershipApplication;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MembershipApplication> */
class MembershipApplicationFactory extends Factory
{
    protected $model = MembershipApplication::class;

    public function definition(): array
    {
        $first = fake()->firstName();
        $last = fake()->lastName();

        return [
            'organization_id' => Organization::factory(),
            'user_id' => null,
            'application_number' => 'APP-' . fake()->unique()->numerify('########'),
            'first_name' => $first,
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => $last,
            'phone' => fake()->numerify('+2547########'),
            'alternate_phone' => null,
            'email' => fake()->safeEmail(),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'national_id' => fake()->optional()->numerify('########'),
            'address' => fake()->optional()->address(),
            'city' => 'Nairobi',
            'county' => 'Nairobi',
            'eligibility_answers' => ['relationship' => 'family'],
            'status' => MembershipApplication::STATUS_DRAFT,
            'submitted_at' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'rejection_reason' => null,
            'approved_member_id' => null,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }

    public function submitted(): static
    {
        return $this->state(fn () => ['status' => MembershipApplication::STATUS_SUBMITTED, 'submitted_at' => now()]);
    }

    public function underReview(): static
    {
        return $this->state(fn () => ['status' => MembershipApplication::STATUS_UNDER_REVIEW, 'submitted_at' => now()]);
    }
}
