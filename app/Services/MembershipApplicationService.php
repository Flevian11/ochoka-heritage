<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MembershipApplication;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class MembershipApplicationService
{
    public function __construct(
        private readonly DatabaseManager $db,
        private readonly MembershipNumberGenerator $numbers,
    ) {}

    public function submit(MembershipApplication $application, ?User $actor = null): MembershipApplication
    {
        return $this->db->transaction(function () use ($application, $actor) {
            $application = MembershipApplication::query()->lockForUpdate()->findOrFail($application->id);
            if (! in_array($application->status, [MembershipApplication::STATUS_DRAFT, MembershipApplication::STATUS_REJECTED], true)) {
                throw ValidationException::withMessages(['status' => 'Only draft or rejected applications can be submitted.']);
            }

            $previous = $application->toArray();
            $application->forceFill([
                'status' => MembershipApplication::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'reviewed_by' => null,
                'reviewed_at' => null,
                'rejection_reason' => null,
            ])->save();
            $this->audit($application, $actor, 'submitted', $previous, $application->fresh()->toArray());
            return $application->fresh();
        });
    }

    public function startReview(MembershipApplication $application, User $actor): MembershipApplication
    {
        return $this->db->transaction(function () use ($application, $actor) {
            $application = MembershipApplication::query()->lockForUpdate()->findOrFail($application->id);
            $this->assertActorOrganization($application->organization_id, $actor);
            if ($application->status !== MembershipApplication::STATUS_SUBMITTED) {
                throw ValidationException::withMessages(['status' => 'Only submitted applications can enter review.']);
            }
            $previous = $application->toArray();
            $application->update(['status' => MembershipApplication::STATUS_UNDER_REVIEW]);
            $this->audit($application, $actor, 'under_review', $previous, $application->fresh()->toArray());
            return $application->fresh();
        });
    }

    public function approve(MembershipApplication $application, User $actor): Member
    {
        return $this->db->transaction(function () use ($application, $actor) {
            $application = MembershipApplication::query()->lockForUpdate()->findOrFail($application->id);
            $this->assertActorOrganization($application->organization_id, $actor);
            if ($application->approved_member_id) {
                return $application->approvedMember()->firstOrFail();
            }
            if (! in_array($application->status, [MembershipApplication::STATUS_SUBMITTED, MembershipApplication::STATUS_UNDER_REVIEW], true)) {
                throw ValidationException::withMessages(['status' => 'Only submitted or under-review applications can be approved.']);
            }

            if ($application->user_id) {
                $existing = Member::withTrashed()->where('user_id', $application->user_id)->first();
                if ($existing) {
                    throw ValidationException::withMessages(['user_id' => 'This account is already linked to a membership.']);
                }
            }

            $organization = Organization::query()->findOrFail($application->organization_id);
            if (! $organization->is_active) {
                throw ValidationException::withMessages(['organization_id' => 'The organization is inactive.']);
            }

            $member = Member::create([
                'organization_id' => $application->organization_id,
                'user_id' => $application->user_id,
                'membership_number' => $this->numbers->generate(),
                'first_name' => $application->first_name,
                'middle_name' => $application->middle_name,
                'last_name' => $application->last_name,
                'phone' => $application->phone,
                'alternate_phone' => $application->alternate_phone,
                'email' => $application->email,
                'date_of_birth' => $application->date_of_birth,
                'national_id' => $application->national_id,
                'address' => $application->address,
                'city' => $application->city,
                'county' => $application->county,
                'joined_at' => now()->toDateString(),
                'status' => Member::STATUS_ACTIVE,
            ]);

            $member->statusHistories()->create([
                'organization_id' => $member->organization_id,
                'from_status' => null,
                'to_status' => Member::STATUS_ACTIVE,
                'reason' => 'Membership application approved.',
                'changed_by' => $actor->id,
                'changed_at' => now(),
            ]);

            $previous = $application->toArray();
            $application->update([
                'status' => MembershipApplication::STATUS_APPROVED,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
                'approved_member_id' => $member->id,
            ]);
            $this->audit($application, $actor, 'approved', $previous, $application->fresh()->toArray());
            return $member->fresh();
        });
    }

    public function reject(MembershipApplication $application, User $actor, string $reason): MembershipApplication
    {
        return $this->db->transaction(function () use ($application, $actor, $reason) {
            $application = MembershipApplication::query()->lockForUpdate()->findOrFail($application->id);
            $this->assertActorOrganization($application->organization_id, $actor);
            if (! in_array($application->status, [MembershipApplication::STATUS_SUBMITTED, MembershipApplication::STATUS_UNDER_REVIEW], true)) {
                throw ValidationException::withMessages(['status' => 'Only submitted or under-review applications can be rejected.']);
            }
            $reason = trim($reason);
            if ($reason === '') {
                throw ValidationException::withMessages(['reason' => 'A rejection reason is required.']);
            }
            $previous = $application->toArray();
            $application->update([
                'status' => MembershipApplication::STATUS_REJECTED,
                'reviewed_by' => $actor->id,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
                'approved_member_id' => null,
            ]);
            $this->audit($application, $actor, 'rejected', $previous, $application->fresh()->toArray(), $reason);
            return $application->fresh();
        });
    }

    public function withdraw(MembershipApplication $application, ?User $actor = null): MembershipApplication
    {
        return $this->db->transaction(function () use ($application, $actor) {
            $application = MembershipApplication::query()->lockForUpdate()->findOrFail($application->id);
            if ($actor) {
                $this->assertActorOrganization($application->organization_id, $actor);
            }
            if (! in_array($application->status, [MembershipApplication::STATUS_DRAFT, MembershipApplication::STATUS_SUBMITTED, MembershipApplication::STATUS_UNDER_REVIEW], true)) {
                throw ValidationException::withMessages(['status' => 'This application can no longer be withdrawn.']);
            }
            $previous = $application->toArray();
            $application->update(['status' => MembershipApplication::STATUS_WITHDRAWN]);
            $this->audit($application, $actor, 'withdrawn', $previous, $application->fresh()->toArray());
            return $application->fresh();
        });
    }

    private function assertActorOrganization(int $organizationId, User $actor): void
    {
        if ((int) $actor->member?->organization_id !== $organizationId) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('The actor does not belong to this organization.');
        }
    }

    private function audit(MembershipApplication $application, ?User $actor, string $action, ?array $previous, ?array $new, ?string $reason = null): void
    {
        AuditLog::create([
            'organization_id' => $application->organization_id,
            'user_id' => $actor?->id,
            'role_name' => $actor?->activeRoles()->value('name'),
            'action' => $action,
            'module' => 'membership_applications',
            'auditable_type' => $application::class,
            'auditable_id' => $application->id,
            'previous_values' => $previous,
            'new_values' => $new,
            'reason' => $reason,
        ]);
    }
}
