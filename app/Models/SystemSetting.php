<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id','key','value','type','group','description','is_public','effective_from','effective_until',
    ];

    protected function casts(): array
    {
        return ['is_public' => 'boolean', 'effective_from' => 'date', 'effective_until' => 'date'];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }

    public function scopeCurrent($query, $date = null)
    {
        $date = $date ? now()->parse($date)->toDateString() : now()->toDateString();
        return $query->whereDate('effective_from', '<=', $date)
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhereDate('effective_until', '>', $date));
    }

    protected static function booted(): void
    {
        static::creating(fn (self $setting) => $setting->ensureOrganizationIntegrity());
        static::updating(function (self $setting) {
            $setting->ensureOrganizationIntegrity();
            if ($setting->isDirty(['organization_id', 'key', 'value', 'type', 'group', 'description', 'is_public', 'effective_from', 'effective_until'])) {
                if ($setting->getOriginal('effective_until') !== null) {
                    throw new \LogicException('Retired system settings are immutable.');
                }
            }
        });
        static::deleting(fn () => throw new \LogicException('System settings are versioned; retire the setting instead of deleting history.'));
    }

    private function ensureOrganizationIntegrity(): void
    {
        $organization = Organization::query()->find($this->organization_id);
        if ($organization && ! $organization->is_active) {
            throw new \InvalidArgumentException('System setting must belong to an active organization.');
        }
        if ($this->effective_until !== null && $this->effective_from !== null && $this->effective_until->lt($this->effective_from)) {
            throw new \InvalidArgumentException('System setting effective_until must be on or after effective_from.');
        }
    }
}
