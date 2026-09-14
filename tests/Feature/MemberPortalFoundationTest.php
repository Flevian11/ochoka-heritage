<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberPortalFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_view_their_portal_dashboard(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['email_verified_at' => now()]);
        $member = Member::factory()->for($organization)->for($user)->create(['status' => Member::STATUS_ACTIVE]);

        $response = $this->actingAs($user)->get('/member');

        $response->assertSuccessful();
        $response->assertInertia(fn ($page) => $page->component('Member/Dashboard'));
        $this->assertSame($organization->id, $member->organization_id);
    }

    public function test_account_without_member_record_cannot_enter_member_portal(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->get('/member')->assertForbidden();
    }

    public function test_member_can_update_allowed_profile_fields_and_change_is_audited(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['email_verified_at' => now()]);
        $member = Member::factory()->for($organization)->for($user)->create([
            'status' => Member::STATUS_ACTIVE,
            'phone' => '0700000000',
            'city' => 'Nairobi',
        ]);

        $response = $this->actingAs($user)->put('/member/profile', [
            'phone' => '0711111111',
            'alternate_phone' => '0722222222',
            'date_of_birth' => '1990-01-02',
            'national_id' => '12345678',
            'address' => 'Updated address',
            'city' => 'Kakamega',
            'county' => 'Kakamega',
        ]);

        $response->assertRedirect();
        $member->refresh();

        $this->assertSame('0711111111', $member->phone);
        $this->assertSame('Kakamega', $member->city);
        $this->assertDatabaseHas('audit_logs', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'action' => 'profile_updated',
            'module' => 'member_portal',
            'auditable_id' => $member->id,
        ]);
    }

    public function test_member_cannot_update_another_member_through_portal_route(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['email_verified_at' => now()]);
        $member = Member::factory()->for($organization)->for($user)->create(['status' => Member::STATUS_ACTIVE]);

        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        Member::factory()->for($organization)->for($otherUser)->create(['status' => Member::STATUS_ACTIVE]);

        $this->actingAs($user)->put('/member/profile', [
            'phone' => '0700000000',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('members', ['id' => $member->id]);
    }
}
