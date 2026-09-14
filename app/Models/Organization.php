<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'legal_name', 'description', 'email', 'phone',
        'alternate_phone', 'website_url', 'address', 'city', 'county',
        'country', 'currency', 'timezone', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function membershipApplications(): HasMany
    {
        return $this->hasMany(MembershipApplication::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(SystemSetting::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function executivePositions(): HasMany
    {
        return $this->hasMany(ExecutivePosition::class);
    }

    public function executiveAppointments(): HasMany
    {
        return $this->hasMany(ExecutiveAppointment::class);
    }
}
