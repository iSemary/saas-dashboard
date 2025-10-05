<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;
use Illuminate\Support\Facades\Hash;

class SimpleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@customer1.local'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'customer_id' => 1, // Required field
                'email_verified_at' => now(),
            ]
        );

        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $user->assignRole($adminRole);
            $this->command->info("Created user: {$user->email} with admin role");
        } else {
            $this->command->error('Admin role not found');
        }
    }
}
