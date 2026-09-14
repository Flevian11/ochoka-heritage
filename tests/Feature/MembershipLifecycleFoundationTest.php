<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MembershipApplication;
use App\Models\Organization;
use App\Models\User;
use App\Services\MemberLifecycleService;
use App\Services\MembershipApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class MembershipLifecycleFoundationTest extends TestCase
{
    use RefreshDatabase;

    private function actor(Organization $organization): array
    {
        $user = User::factory()->create();
        $member = Member::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'status' => Member::STATUS_ACTIVE,
        ]);

        return [$user, $member];
    }

    public function test_application_can_be_submitted_without_creating_a_member(): void
    {
        $organization = Organization::factory()->create();
        $application = MembershipApplication::factory()->create(['organization_id' => $organization->id]);

        app(MembershipApplicationService::class)->submit($application);

        $application->refresh();
        $this->assertSame(MembershipApplication::STATUS_SUBMITTED, $application->status);
        $this->assertNotNull($application->submitted_at);
        $this->assertDatabaseCount('members', 0);
        $this->assertDatabaseHas('audit_logs', [
            'organization_id' => $organization->id,
            'module' => 'membership_applications',
            'action' => 'submitted',
            'auditable_id' => $application->id,
        ]);
    }

    public function test_application_can_move_into_review(): void
    {
        $organization = Organization::factory()->create();
        [$actor] = $this->actor($organization);
        $application = MembershipApplication::factory()->submitted()->create(['organization_id' => $organization->id]);

        app(MembershipApplicationService::class)->startReview($application, $actor);

        $this->assertSame(MembershipApplication::STATUS_UNDER_REVIEW, $application->fresh()->status);
    }

    public function test_approval_creates_member_and_preserves_application_history(): void
    {
        $organization = Organization::factory()->create();
        [$actor] = $this->actor($organization);
        $application = MembershipApplication::factory()->underReview()->create([
            'organization_id' => $organization->id,
            'first_name' => 'Flevian',
            'last_name' => 'Ochoka',
        ]);

        $member = app(MembershipApplicationService::class)->approve($application, $actor);

        $this->assertSame($organization->id, $member->organization_id);
        $this->assertSame(Member::STATUS_ACTIVE, $member->status);
        $this->assertNotEmpty($member->membership_number);
        $this->assertSame(MembershipApplication::STATUS_APPROVED, $application->fresh()->status);
        $this->assertSame($member->id, $application->fresh()->approved_member_id);
        $this->assertDatabaseHas('member_status_histories', [
            'member_id' => $member->id,
            'from_status' => null,
            'to_status' => Member::STATUS_ACTIVE,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'organization_id' => $organization->id,
            'module' => 'membership_applications',
            'action' => 'approved',
            'auditable_id' => $application->id,
        ]);
    }

    public function test_rejection_requires_a_reason_and_preserves_application(): void
    {
        $organization = Organization::factory()->create();
        [$actor] = $this->actor($organization);
        $application = MembershipApplication::factory()->submitted()->create(['organization_id' => $organization->id]);

        $this->expectException(ValidationException::class);
        app(MembershipApplicationService::class)->reject($application, $actor, '');

        $application->refresh();
        $this->assertSame(MembershipApplication::STATUS_SUBMITTED, $application->status);
    }

    public function test_rejected_application_can_be_resubmitted(): void
    {
        $organization = Organization::factory()->create();
        [$actor] = $this->actor($organization);
        $application = MembershipApplication::factory()->submitted()->create(['organization_id' => $organization->id]);
        app(MembershipApplicationService::class)->reject($application, $actor, 'Eligibility evidence was incomplete.');

        app(MembershipApplicationService::class)->submit($application, $actor);

        $application->refresh();
        $this->assertSame(MembershipApplication::STATUS_SUBMITTED, $application->status);
        $this->assertNull($application->rejection_reason);
    }

    public function test_member_status_transition_requires_valid_transition_and_reason(): void
    {
        $organization = Organization::factory()->create();
        [$actor, $member] = $this->actor($organization);

        app(MemberLifecycleService::class)->transition($member, Member::STATUS_SUSPENDED, $actor, 'Temporary suspension pending review.');
        $this->assertSame(Member::STATUS_SUSPENDED, $member->fresh()->status);

        app(MemberLifecycleService::class)->transition($member, Member::STATUS_ACTIVE, $actor, 'Review completed.');
        $this->assertSame(Member::STATUS_ACTIVE, $member->fresh()->status);

        $this->assertSame(2, $member->statusHistories()->count());
    }

    public function test_deceased_member_is_terminal(): void
    {
        $organization = Organization::factory()->create();
        [$actor, $member] = $this->actor($organization);

        app(MemberLifecycleService::class)->transition($member, Member::STATUS_DECEASED, $actor, 'Death reported and verified.');

        $this->expectException(ValidationException::class);
        app(MemberLifecycleService::class)->transition($member, Member::STATUS_ACTIVE, $actor, 'Attempted reactivation.');
    }

    public function test_member_status_transition_rejects_cross_organization_actor(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        [$actor] = $this->actor($otherOrganization);
        $member = Member::factory()->create(['organization_id' => $organization->id]);

        $this->expectException(AccessDeniedHttpException::class);
        app(MemberLifecycleService::class)->transition($member, Member::STATUS_SUSPENDED, $actor, 'Invalid cross-organization attempt.');
    }

    public function test_application_approval_is_idempotent(): void
    {
        $organization = Organization::factory()->create();
        [$actor] = $this->actor($organization);
        $application = MembershipApplication::factory()->submitted()->create(['organization_id' => $organization->id]);

        $memberCountBeforeApproval = Member::where('organization_id', $organization->id)->count();

        $member = app(MembershipApplicationService::class)->approve($application, $actor);
        $sameMember = app(MembershipApplicationService::class)->approve($application, $actor);

        $this->assertSame($member->id, $sameMember->id);
        $this->assertSame(1, Member::where('id', $member->id)->count());
        $this->assertSame($memberCountBeforeApproval + 1, Member::where('organization_id', $organization->id)->count());
    }

    public function test_application_rejects_actor_from_another_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        [$actor] = $this->actor($otherOrganization);
        $application = MembershipApplication::factory()->submitted()->create(['organization_id' => $organization->id]);

        $this->expectException(AccessDeniedHttpException::class);
        app(MembershipApplicationService::class)->startReview($application, $actor);
    }
}
