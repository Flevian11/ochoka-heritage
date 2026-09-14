<?php

namespace Database\Factories;

use App\Models\ElectionNomination;
use App\Models\ElectionNominationSeconder;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class ElectionNominationSeconderFactory extends Factory
{
    protected $model = ElectionNominationSeconder::class;

    public function definition(): array
    {
        return [
            'nomination_id' => ElectionNomination::factory(),
            'member_id' => Member::factory(),
        ];
    }
}