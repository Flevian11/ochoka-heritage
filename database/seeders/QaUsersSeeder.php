<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class QaUsersSeeder extends Seeder
{
    /**
     * Seed the dedicated QA accounts used for authentication testing.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'yalitechsystems@gmail.com',
                'name' => 'TechGhost',
                'password' => 'Password123!',
                'phone' => '+254795323141',
            ],
            [
                'email' => '3ochok@gmail.com',
                'name' => 'Ochoka Member',
                'password' => 'Password123!',
                'phone' => '+254738628446',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'status' => 'active',
                    'phone' => $data['phone'],
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ],
            );
        }
    }
}