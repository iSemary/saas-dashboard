<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dummyUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'username' => 'john_doe',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'username' => 'jane_smith',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'username' => 'mike_johnson',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@example.com',
                'username' => 'sarah_wilson',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@example.com',
                'username' => 'david_brown',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($dummyUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Dummy users seeded successfully!');
    }
}
