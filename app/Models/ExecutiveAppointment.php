<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExecutiveAppointment extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_REVOKED = 'revoked';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = ['organization_id','member_id','executive_position_id','starts_at','ends_at','status','appointed_by','appointed_at','revoked_at','revoked_by','reason'];

    protected function casts(): array
    {
        return ['starts_at'=>'date','ends_at'=>'date','appointed_at'=>'datetime','revoked_at'=>'datetime'];
    }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function position(): BelongsTo { return $this->belongsTo(ExecutivePosition::class, 'executive_position_id'); }
    public function appointedBy(): BelongsTo { return $this->belongsTo(User::class, 'appointed_by'); }
    public function revokedBy(): BelongsTo { return $this->belongsTo(User::class, 'revoked_by'); }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->revoked_at === null
            && $this->starts_at?->isPast() && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    protected static function booted(): void
    {
        static::creating(fn (self $appointment) => $appointment->ensureOrganizationIntegrity());
        static::updating(function (self $appointment) {
            $appointment->ensureOrganizationIntegrity();
            if ($appointment->getOriginal('status') === self::STATUS_REVOKED) {
                throw new \LogicException('Revoked executive appointments are immutable.');
            }
        });
    }

    private function ensureOrganizationIntegrity(): void
    {
        $member = Member::query()->find($this->member_id);
        $position = ExecutivePosition::withTrashed()->find($this->executive_position_id);
        if ($member && (int) $member->organization_id !== (int) $this->organization_id) {
            throw new \InvalidArgumentException('Executive appointment member must belong to the same organization.');
        }
        if ($position && (int) $position->organization_id !== (int) $this->organization_id) {
            throw new \InvalidArgumentException('Executive appointment position must belong to the same organization.');
        }
    }
}
