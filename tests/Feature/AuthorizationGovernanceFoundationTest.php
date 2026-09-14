<?php

namespace Tests\Feature;

use App\Models\ExecutiveAppointment;
use App\Models\ExecutivePosition;
use App\Models\Member;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationGovernanceFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_an_active_role_and_permission(): void
    {
        $organization = Organization::factory()->create();
        $role = Role::factory()->create(['organization_id' => $organization->id]);
        $permission = Permission::factory()->create(['key' => 'members.view']);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->roles()->attach($role, ['assigned_at' => now()]);

        $this->assertTrue($user->hasPermission('members.view'));
    }

    public function test_revoked_role_no_longer_grants_permission(): void
    {
        $organization = Organization::factory()->create();
        $role = Role::factory()->create(['organization_id' => $organization->id]);
        $permission = Permission::factory()->create(['key' => 'members.view']);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->roles()->attach($role, [
            'assigned_at' => now(),
            'revoked_at' => now(),
        ]);

        $this->assertFalse($user->hasPermission('members.view'));
    }

    public function test_executive_appointment_belongs_to_member_and_position(): void
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->create(['organization_id' => $organization->id]);
        $position = ExecutivePosition::factory()->create(['organization_id' => $organization->id]);

        $appointment = ExecutiveAppointment::factory()->create([
            'organization_id' => $organization->id,
            'member_id' => $member->id,
            'executive_position_id' => $position->id,
        ]);

        $this->assertTrue($appointment->member->is($member));
        $this->assertTrue($appointment->position->is($position));
        $this->assertTrue($member->executiveAppointments->contains($appointment));
    }
}
