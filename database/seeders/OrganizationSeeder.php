<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organization::updateOrCreate(
            ['slug' => 'ochoka-heritage'],
            [
                'name' => 'Ochoka Heritage',
                'legal_name' => null,
                'description' => null,
                'email' => null,
                'phone' => null,
                'alternate_phone' => null,
                'website_url' => null,
                'address' => null,
                'city' => null,
                'county' => null,
                'country' => 'Kenya',
                'currency' => 'KES',
                'timezone' => 'Africa/Nairobi',
                'is_active' => true,
            ]
        );
    }
}
