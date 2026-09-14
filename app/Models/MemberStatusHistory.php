<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'member_id', 'from_status', 'to_status', 'reason', 'changed_by', 'changed_at',
    ];

    protected function casts(): array { return ['changed_at' => 'datetime']; }

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function changedBy(): BelongsTo { return $this->belongsTo(User::class, 'changed_by'); }

    protected static function booted(): void
    {
        static::saving(function (MemberStatusHistory $history): void {
            $member = Member::find($history->member_id);
            if ($member && (int) $member->organization_id !== (int) $history->organization_id) {
                throw new \InvalidArgumentException('The member status history must belong to the same organization as the member.');
            }
            if ($history->changed_by) {
                $actor = User::find($history->changed_by);
                if ($actor?->member && (int) $actor->member->organization_id !== (int) $history->organization_id) {
                    throw new \InvalidArgumentException('The status-change actor must belong to the same organization.');
                }
            }
        });
        static::updating(fn () => throw new \LogicException('Member status history is immutable and cannot be updated.'));
        static::deleting(fn () => throw new \LogicException('Member status history is immutable and cannot be deleted.'));
    }
}
