<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DECEASED = 'deceased';

    protected $fillable = [
        'organization_id', 'user_id', 'membership_number', 'first_name',
        'middle_name', 'last_name', 'phone', 'alternate_phone', 'email',
        'date_of_birth', 'national_id', 'address', 'city', 'county',
        'joined_at', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joined_at' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(MemberStatusHistory::class)->orderByDesc('changed_at');
    }

    public function executiveAppointments(): HasMany
    {
        return $this->hasMany(ExecutiveAppointment::class);
    }

    public function activeExecutiveAppointments(): HasMany
    {
        return $this->executiveAppointments()
            ->where('status', 'active')
            ->whereNull('revoked_at');
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name, $this->middle_name, $this->last_name,
        ])));
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function hasAccount(): bool
    {
        return $this->user_id !== null;
    }
}
