<?php

namespace App\Services;

use App\Models\Member;
use App\Models\WelfareCase;
use App\Models\WelfareContribution;
use InvalidArgumentException;

class WelfareContributionService
{
    public function create(array $attributes): WelfareContribution
    {
        $case = WelfareCase::query()->findOrFail($attributes['welfare_case_id']);
        $member = Member::query()->findOrFail($attributes['member_id']);
        $organizationId = (int) ($attributes['organization_id'] ?? $case->organization_id);

        if ((int) $case->organization_id !== $organizationId) {
            throw new InvalidArgumentException('Welfare case and contribution must belong to the same organization.');
        }

        if ((int) $member->organization_id !== $organizationId) {
            throw new InvalidArgumentException('Welfare member and contribution must belong to the same organization.');
        }

        $attributes['organization_id'] = $organizationId;

        return WelfareContribution::query()->create($attributes);
    }
}
