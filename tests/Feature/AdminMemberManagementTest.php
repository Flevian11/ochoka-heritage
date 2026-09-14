<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminMemberManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $permission = 'members.manage'): array
    {
        $organization = Organization::factory()->create();
        $member = Member::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => null,
        ]);

        $user = User::factory()->create(['password' => Hash::make('password')]);
        $member->update(['user_id' => $user->id]);

        $role = Role::factory()->create(['organization_id' => $organization->id]);

        $permissions = [$permission];
        if ($permission === 'members.manage') {
            $permissions[] = 'members.view';
        }

        foreach (array_unique($permissions) as $key) {
            $permissionModel = Permission::factory()->create(['key' => $key]);
            $role->permissions()->attach($permissionModel);
        }

        $user->roles()->attach($role, ['assigned_at' => now()]);

        return [$organization, $user, $member];
    }

    public function test_admin_can_sign_in_and_view_member_dashboard(): void
    {
        [$organization, $user] = $this->admin('members.view');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_member_management_is_permission_protected(): void
    {
        [, $user] = $this->admin('members.view');

        $this->actingAs($user)->get('/admin/members/create')->assertForbidden();
    }

    public function test_admin_can_create_member_in_their_organization_and_audit_it(): void
    {
        [$organization, $user] = $this->admin();

        $response = $this->actingAs($user)->post('/admin/members', [
            'membership_number' => 'OH-NEW001',
            'first_name' => 'Test',
            'last_name' => 'Member',
            'status' => Member::STATUS_PENDING,
        ]);

        $response->assertRedirect('/admin/members');

        $member = Member::where('membership_number', 'OH-NEW001')->firstOrFail();
        $this->assertSame($organization->id, $member->organization_id);
        $this->assertDatabaseHas('audit_logs', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'module' => 'members',
            'action' => 'created',
            'auditable_id' => $member->id,
        ]);
    }

    public function test_admin_cannot_edit_member_from_another_organization(): void
    {
        [, $user] = $this->admin();
        $other = Organization::factory()->create();
        $member = Member::factory()->create(['organization_id' => $other->id]);

        $this->actingAs($user)->get("/admin/members/{$member->id}/edit")->assertNotFound();
    }
}
