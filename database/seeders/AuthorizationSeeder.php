<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('slug', 'ochoka-heritage')->firstOrFail();

        $permissions = [
            ['key' => 'system.manage', 'name' => 'Manage system', 'group' => 'system'],
            ['key' => 'governance.view', 'name' => 'View governance', 'group' => 'governance'],
            ['key' => 'governance.manage', 'name' => 'Manage governance', 'group' => 'governance'],
            ['key' => 'members.view', 'name' => 'View members', 'group' => 'members'],
            ['key' => 'members.manage', 'name' => 'Manage members', 'group' => 'members'],
            ['key' => 'finance.view', 'name' => 'View finances', 'group' => 'finance'],
            ['key' => 'finance.manage', 'name' => 'Manage finances', 'group' => 'finance'],
            ['key' => 'welfare.view', 'name' => 'View welfare', 'group' => 'welfare'],
            ['key' => 'welfare.manage', 'name' => 'Manage welfare', 'group' => 'welfare'],
            ['key' => 'meetings.view', 'name' => 'View meetings', 'group' => 'meetings'],
            ['key' => 'meetings.manage', 'name' => 'Manage meetings', 'group' => 'meetings'],
            ['key' => 'elections.view', 'name' => 'View elections', 'group' => 'elections'],
            ['key' => 'elections.manage', 'name' => 'Manage elections', 'group' => 'elections'],
            ['key' => 'reports.view', 'name' => 'View reports', 'group' => 'reports'],
            ['key' => 'communications.manage', 'name' => 'Manage communications', 'group' => 'communications'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['key' => $permission['key']], $permission);
        }

        $roleDefinitions = [
            'super-admin' => [
                'name' => 'Super Admin',
                'description' => 'Full system administration.',
                'permissions' => Permission::pluck('id')->all(),
            ],
            'chairman' => [
                'name' => 'Chairman',
                'description' => 'Governance, welfare and organizational oversight.',
                'permissions' => [
                    'members.view', 'governance.view', 'governance.manage', 'welfare.view', 'welfare.manage',
                    'meetings.view', 'meetings.manage', 'elections.view',
                    'elections.manage', 'reports.view', 'communications.manage',
                ],
            ],
            'treasurer' => [
                'name' => 'Treasurer',
                'description' => 'Financial management and reporting.',
                'permissions' => [
                    'members.view', 'finance.view', 'finance.manage',
                    'welfare.view', 'welfare.manage', 'reports.view',
                ],
            ],
            'secretary' => [
                'name' => 'Secretary',
                'description' => 'Meetings, records and communications.',
                'permissions' => [
                    'members.view', 'governance.view', 'meetings.view', 'meetings.manage',
                    'communications.manage', 'reports.view',
                ],
            ],
            'executive' => [
                'name' => 'Executive',
                'description' => 'Executive oversight.',
                'permissions' => [
                    'members.view', 'welfare.view', 'meetings.view', 'elections.view',
                    'reports.view',
                ],
            ],
            'member' => [
                'name' => 'Member',
                'description' => 'Standard member access.',
                'permissions' => [
                    'welfare.view', 'meetings.view', 'elections.view',
                ],
            ],
        ];

        foreach ($roleDefinitions as $slug => $definition) {
            $role = Role::updateOrCreate(
                ['organization_id' => $organization->id, 'slug' => $slug],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'is_system' => true,
                    'is_active' => true,
                ],
            );

            $permissionIds = collect($definition['permissions'])
                ->map(fn ($permission) => is_numeric($permission)
                    ? (int) $permission
                    : Permission::where('key', $permission)->value('id'))
                ->filter()
                ->values()
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
