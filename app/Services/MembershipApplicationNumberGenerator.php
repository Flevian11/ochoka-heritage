<?php

namespace App\Services;

use App\Models\MembershipApplication;
use Illuminate\Support\Str;

class MembershipApplicationNumberGenerator
{
    public function generate(): string
    {
        do {
            $number = 'APP-' . Str::upper(Str::random(10));
        } while (MembershipApplication::query()->where('application_number', $number)->exists());

        return $number;
    }
}
