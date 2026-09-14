<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Member> */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'organization_id' => Organization::factory(),
            'user_id' => null,
            'membership_number' => 'OH-' . fake()->unique()->numerify('######'),
            'first_name' => $firstName,
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => $lastName,
            'phone' => fake()->optional()->numerify('+2547########'),
            'email' => fake()->optional()->safeEmail(),
            'date_of_birth' => fake()->optional()->dateTimeBetween('-70 years', '-18 years')?->format('Y-m-d'),
            'joined_at' => fake()->optional()->dateTimeBetween('-5 years', 'now')?->format('Y-m-d'),
            'status' => Member::STATUS_ACTIVE,
            'notes' => null,
        ];
    }

    public function withAccount(): static
    {
        return $this->state(fn () => [
            'user_id' => User::factory(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => Member::STATUS_PENDING]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => Member::STATUS_SUSPENDED]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => Member::STATUS_INACTIVE]);
    }
}
