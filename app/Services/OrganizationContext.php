<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class OrganizationContext
{
    public function forUser(User $user): Organization
    {
        $organization = $user->member?->organization
            ?? $user->activeRoles()->with('organization')->first()?->organization;

        if (! $organization || ! $organization->is_active) {
            throw new AuthorizationException('No active organization is assigned to this account.');
        }

        return $organization;
    }
}