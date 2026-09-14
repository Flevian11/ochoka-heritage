<?php
namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\ExecutiveAppointment;
use App\Models\ExecutivePosition;
use App\Models\Member;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminGovernanceSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $permission='governance.manage'): array {
        $org=Organization::factory()->create(); $member=Member::factory()->create(['organization_id'=>$org->id,'user_id'=>null]);
        $user=User::factory()->create(['password'=>Hash::make('password')]); $member->update(['user_id'=>$user->id]); $role=Role::factory()->create(['organization_id'=>$org->id]);
        $keys=[$permission]; if($permission==='governance.manage') $keys[]='governance.view';
        foreach(array_unique($keys) as $key) $role->permissions()->attach(Permission::factory()->create(['key'=>$key]));
        $user->roles()->attach($role,['assigned_at'=>now()]); return [$org,$user,$member];
    }

    public function test_governance_is_permission_protected(): void { [, $user]=$this->admin('members.view'); $this->actingAs($user)->get('/admin/governance/positions')->assertForbidden(); }
    public function test_admin_can_create_position_in_own_organization(): void { [$org,$user]=$this->admin(); $this->actingAs($user)->post('/admin/governance/positions',['name'=>'Chairman','slug'=>'chairman','description'=>'Leads the association.','display_order'=>1,'is_active'=>true])->assertRedirect('/admin/governance/positions'); $this->assertDatabaseHas('executive_positions',['organization_id'=>$org->id,'slug'=>'chairman']); }
    public function test_admin_cannot_edit_position_from_another_organization(): void { [, $user]=$this->admin(); $other=Organization::factory()->create(); $position=ExecutivePosition::factory()->create(['organization_id'=>$other->id]); $this->actingAs($user)->get("/admin/governance/positions/{$position->id}/edit")->assertNotFound(); }
    public function test_appointment_requires_same_organization(): void { [$org,$user]=$this->admin(); $position=ExecutivePosition::factory()->create(['organization_id'=>$org->id]); $other=Organization::factory()->create(); $otherMember=Member::factory()->create(['organization_id'=>$other->id]); $this->actingAs($user)->post('/admin/governance/appointments',['member_id'=>$otherMember->id,'executive_position_id'=>$position->id,'starts_at'=>now()->toDateString()])->assertSessionHasErrors('member_id'); $this->assertDatabaseCount('executive_appointments',0); }
    public function test_position_cannot_have_overlapping_active_appointments(): void { [$org,$user,$member]=$this->admin(); $position=ExecutivePosition::factory()->create(['organization_id'=>$org->id]); $this->actingAs($user)->post('/admin/governance/appointments',['member_id'=>$member->id,'executive_position_id'=>$position->id,'starts_at'=>'2026-01-01','ends_at'=>'2026-12-31'])->assertRedirect('/admin/governance/appointments'); $member2=Member::factory()->create(['organization_id'=>$org->id]); $this->actingAs($user)->post('/admin/governance/appointments',['member_id'=>$member2->id,'executive_position_id'=>$position->id,'starts_at'=>'2026-06-01','ends_at'=>'2027-01-01'])->assertSessionHasErrors('executive_position_id'); $this->assertDatabaseCount('executive_appointments',1); }
    public function test_active_appointment_can_be_revoked_and_audited(): void { [$org,$user,$member]=$this->admin(); $position=ExecutivePosition::factory()->create(['organization_id'=>$org->id]); $appointment=ExecutiveAppointment::create(['organization_id'=>$org->id,'member_id'=>$member->id,'executive_position_id'=>$position->id,'starts_at'=>'2026-01-01','status'=>'active','appointed_by'=>$user->id,'appointed_at'=>now()]); $this->actingAs($user)->post("/admin/governance/appointments/{$appointment->id}/revoke",['reason'=>'Term ended'])->assertSessionHasNoErrors(); $this->assertDatabaseHas('executive_appointments',['id'=>$appointment->id,'status'=>'revoked','revoked_by'=>$user->id]); }
    public function test_system_settings_are_permission_protected(): void { [, $user]=$this->admin('governance.view'); $this->actingAs($user)->get('/admin/settings')->assertForbidden(); }
    public function test_system_admin_can_create_version_and_retire_without_losing_history(): void { [$org,$user]=$this->admin('system.manage'); $this->actingAs($user)->post('/admin/settings',['key'=>'contribution.monthly_minimum','value'=>'200','type'=>'integer','group'=>'contributions','description'=>'Monthly minimum.','is_public'=>false,'effective_from'=>'2026-09-14'])->assertSessionHasNoErrors(); $setting=SystemSetting::where('organization_id',$org->id)->where('key','contribution.monthly_minimum')->firstOrFail(); $this->actingAs($user)->put("/admin/settings/{$setting->id}",['value'=>'250','type'=>'integer','group'=>'contributions','description'=>'Updated.','is_public'=>true,'effective_from'=>'2026-10-01'])->assertSessionHasNoErrors(); $this->assertDatabaseHas('system_settings',['organization_id'=>$org->id,'key'=>'contribution.monthly_minimum','value'=>'200']); $this->assertDatabaseHas('system_settings',['organization_id'=>$org->id,'key'=>'contribution.monthly_minimum','value'=>'250']); $current=SystemSetting::where('organization_id',$org->id)->where('key','contribution.monthly_minimum')->current('2026-10-15')->firstOrFail(); $this->assertSame('250',$current->value); $this->actingAs($user)->delete("/admin/settings/{$current->id}")->assertSessionHasNoErrors(); $this->assertDatabaseHas('system_settings',['id'=>$current->id]); $this->assertNotNull(SystemSetting::find($current->id)->effective_until); $this->assertFalse(SystemSetting::whereKey($current->id)->current('2026-10-15')->exists()); $this->assertDatabaseHas('audit_logs',['organization_id'=>$org->id,'module'=>'settings','action'=>'retired']); }
    public function test_setting_rejects_invalid_typed_value(): void { [, $user]=$this->admin('system.manage'); $this->actingAs($user)->post('/admin/settings',['key'=>'bad.integer','value'=>'abc','type'=>'integer','group'=>'general','description'=>'','is_public'=>false])->assertSessionHasErrors('value'); }
    public function test_permissions_are_organization_scoped(): void { [$orgA,$user]=$this->admin('governance.view'); $orgB=Organization::factory()->create(); $roleB=Role::factory()->create(['organization_id'=>$orgB->id,'is_active'=>true]); $perm=Permission::factory()->create(['key'=>'finance.manage']); $roleB->permissions()->attach($perm); $user->roles()->attach($roleB,['assigned_at'=>now()]); $this->assertFalse($user->fresh()->hasPermission('finance.manage')); $this->assertTrue($user->fresh()->hasPermission('governance.view')); }
    public function test_inactive_role_does_not_grant_permission(): void { [$org,$user]=$this->admin('governance.view'); $role=Role::factory()->create(['organization_id'=>$org->id,'is_active'=>false]); $perm=Permission::factory()->create(['key'=>'finance.manage']); $role->permissions()->attach($perm); $user->roles()->attach($role,['assigned_at'=>now()]); $this->assertFalse($user->fresh()->hasPermission('finance.manage')); }
    public function test_audit_logs_are_immutable(): void { $log=AuditLog::factory()->create(); $this->expectException(\LogicException::class); $log->update(['action'=>'tampered']); }
    public function test_audit_logs_cannot_be_deleted(): void { $log=AuditLog::factory()->create(); $this->expectException(\LogicException::class); $log->delete(); }
    public function test_soft_deleted_role_does_not_grant_permission(): void { [$org,$user]=$this->admin('governance.view'); $role=Role::factory()->create(['organization_id'=>$org->id,'is_active'=>true]); $perm=Permission::factory()->create(['key'=>'finance.manage']); $role->permissions()->attach($perm); $user->roles()->attach($role,['assigned_at'=>now()]); $role->delete(); $this->assertFalse($user->fresh()->hasPermission('finance.manage')); }
    public function test_revoked_appointment_is_immutable(): void { [$org,$user,$member]=$this->admin(); $position=ExecutivePosition::factory()->create(['organization_id'=>$org->id]); $appointment=ExecutiveAppointment::create(['organization_id'=>$org->id,'member_id'=>$member->id,'executive_position_id'=>$position->id,'starts_at'=>'2026-01-01','status'=>'revoked','revoked_at'=>now()]); $this->expectException(\LogicException::class); $appointment->update(['reason'=>'tampered']); }
}
