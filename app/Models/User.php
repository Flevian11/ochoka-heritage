<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function member(): HasOne { return $this->hasOne(Member::class); }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['assigned_at', 'revoked_at', 'assigned_by', 'reason'])
            ->withTimestamps();
    }

    public function activeRoles(): BelongsToMany
    {
        return $this->roles()
            ->wherePivotNull('revoked_at')
            ->where('roles.is_active', true);
    }

    public function hasPermission(string $permission): bool
    {
        $organizationId = $this->member?->organization_id;
        if (! $organizationId) {
            return false;
        }

        return $this->activeRoles()
            ->where('roles.organization_id', $organizationId)
            ->whereHas('permissions', fn ($query) => $query->where('key', $permission))
            ->exists();
    }
}
