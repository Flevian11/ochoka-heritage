<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationMemberRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_belongs_to_organization_and_user(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $member = Member::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($member->organization->is($organization));
        $this->assertTrue($member->user->is($user));
        $this->assertSame($member->id, $user->member->id);
        $this->assertSame($member->id, $organization->members->first()->id);
    }

    public function test_member_can_exist_without_a_user_account(): void
    {
        $member = Member::factory()->create();

        $this->assertFalse($member->hasAccount());
        $this->assertNull($member->user);
        $this->assertTrue($member->isActive());
    }

    public function test_member_status_factory_states_are_available(): void
    {
        $this->assertSame(Member::STATUS_PENDING, Member::factory()->pending()->create()->status);
        $this->assertSame(Member::STATUS_SUSPENDED, Member::factory()->suspended()->create()->status);
        $this->assertSame(Member::STATUS_INACTIVE, Member::factory()->inactive()->create()->status);
    }
}
