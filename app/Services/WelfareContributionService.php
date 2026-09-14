<?php

namespace App\Services;

use App\Models\Member;
use App\Models\WelfareCase;
use App\Models\WelfareContribution;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WelfareContributionService
{
    public function create(array $attributes): WelfareContribution
    {
        $welfareCase = WelfareCase::query()->findOrFail($attributes['welfare_case_id']);
        $member = Member::query()->findOrFail($attributes['member_id']);

        $organizationId = (int) ($attributes['organization_id'] ?? $welfareCase->organization_id);

        if ((int) $welfareCase->organization_id !== $organizationId) {
            throw new InvalidArgumentException('The welfare case must belong to the selected organization.');
        }

        if ((int) $member->organization_id !== $organizationId) {
            throw new InvalidArgumentException('The member must belong to the same organization as the welfare case.');
        }

        return DB::transaction(fn () => WelfareContribution::query()->create([
            ...$attributes,
            'organization_id' => $organizationId,
        ]));
    }
}
