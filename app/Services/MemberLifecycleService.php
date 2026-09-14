<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class MemberLifecycleService
{
    private const TRANSITIONS = [
        Member::STATUS_PENDING => [Member::STATUS_ACTIVE, Member::STATUS_INACTIVE],
        Member::STATUS_ACTIVE => [Member::STATUS_SUSPENDED, Member::STATUS_INACTIVE, Member::STATUS_DECEASED],
        Member::STATUS_SUSPENDED => [Member::STATUS_ACTIVE, Member::STATUS_INACTIVE, Member::STATUS_DECEASED],
        Member::STATUS_INACTIVE => [Member::STATUS_ACTIVE, Member::STATUS_DECEASED],
        Member::STATUS_DECEASED => [],
    ];

    public function __construct(private readonly DatabaseManager $db) {}

    public function transition(Member $member, string $toStatus, User $actor, string $reason): Member
    {
        return $this->db->transaction(function () use ($member, $toStatus, $actor, $reason) {
            $member = Member::query()->lockForUpdate()->findOrFail($member->id);
            if ((int) $member->organization_id !== (int) $actor->member?->organization_id) {
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('The actor does not belong to this organization.');
            }
            if (! in_array($toStatus, [
                Member::STATUS_PENDING, Member::STATUS_ACTIVE, Member::STATUS_SUSPENDED,
                Member::STATUS_INACTIVE, Member::STATUS_DECEASED,
            ], true)) {
                throw ValidationException::withMessages(['status' => 'Invalid membership status.']);
            }
            $reason = trim($reason);
            if ($reason === '') {
                throw ValidationException::withMessages(['reason' => 'A reason is required for a membership status change.']);
            }
            if (! in_array($toStatus, self::TRANSITIONS[$member->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => "Cannot change membership status from {$member->status} to {$toStatus}."]);
            }

            $fromStatus = $member->status;
            $member->update(['status' => $toStatus]);
            $member->statusHistories()->create([
                'organization_id' => $member->organization_id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'reason' => $reason,
                'changed_by' => $actor->id,
                'changed_at' => now(),
            ]);

            AuditLog::create([
                'organization_id' => $member->organization_id,
                'user_id' => $actor->id,
                'role_name' => $actor->activeRoles()->value('name'),
                'action' => 'status_changed',
                'module' => 'members',
                'auditable_type' => $member::class,
                'auditable_id' => $member->id,
                'previous_values' => ['status' => $fromStatus],
                'new_values' => ['status' => $toStatus],
                'reason' => $reason,
            ]);

            return $member->fresh();
        });
    }
}
