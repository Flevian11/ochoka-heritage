<?php

namespace Database\Factories;

use App\Models\Election;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ElectionFactory extends Factory
{
    protected $model = Election::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->words(3, true).' Election',
            'slug' => Str::slug(fake()->words(3, true).'-'.fake()->numberBetween(1000, 9999)),
            'description' => fake()->sentence(),
            'eligibility_criteria' => ['membership_status' => 'active'],
            'status' => Election::DRAFT,
            'created_by' => null,
            'approved_by' => null,
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Election $election) {
            if ($election->created_by === null) {
                $election->created_by = User::factory()->create()->id;
            }
        });
    }
}
