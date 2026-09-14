<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_can_record_actor_context_and_changes(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $member = Member::factory()->for($organization)->for($user)->create();

        $audit = AuditLog::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_name' => 'Treasurer',
            'action' => 'updated',
            'module' => 'members',
            'auditable_type' => Member::class,
            'auditable_id' => $member->id,
            'previous_values' => ['status' => 'pending'],
            'new_values' => ['status' => 'active'],
            'reason' => 'Membership verification completed.',
            'reference' => 'MEM-VERIFY-001',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'id' => $audit->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'action' => 'updated',
            'module' => 'members',
            'auditable_type' => Member::class,
            'auditable_id' => $member->id,
        ]);

        $this->assertSame('pending', $audit->previous_values['status']);
        $this->assertSame('active', $audit->new_values['status']);
    }

    public function test_audit_log_can_survive_deleted_actor_and_organization(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $audit = AuditLog::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);

        $user->delete();
        $organization->delete();

        $audit->refresh();

        $this->assertNull($audit->user_id);
        $this->assertSame($organization->id, $audit->organization_id);
        $this->assertDatabaseHas('audit_logs', ['id' => $audit->id]);
    }
}
