<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Str;

class MembershipNumberGenerator
{
    public function generate(): string
    {
        do {
            $number = 'OH-' . Str::upper(Str::random(8));
        } while (Member::withTrashed()->where('membership_number', $number)->exists());

        return $number;
    }
}
