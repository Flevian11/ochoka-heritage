<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'role_name' => fake()->randomElement([
                'Super Admin',
                'Chairman',
                'Treasurer',
                'Secretary',
                'Executive',
                'Member',
            ]),
            'action' => fake()->randomElement([
                'created',
                'updated',
                'approved',
                'rejected',
                'revoked',
            ]),
            'module' => fake()->randomElement([
                'members',
                'governance',
                'finance',
                'welfare',
                'elections',
                'meetings',
            ]),
            'auditable_type' => null,
            'auditable_id' => null,
            'previous_values' => null,
            'new_values' => null,
            'reason' => fake()->optional()->sentence(),
            'reference' => fake()->optional()->bothify('REF-####-????'),
            'ip_address' => fake()->optional()->ipv4(),
            'user_agent' => fake()->optional()->userAgent(),
            'created_at' => now(),
        ];
    }
}
