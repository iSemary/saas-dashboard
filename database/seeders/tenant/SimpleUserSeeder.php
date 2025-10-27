<?php

namespace Database\Seeders\tenant;

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

        // Try to assign the highest role available (super_admin, then owner, then admin)
        $highestRole = null;
        
        // First try super_admin
        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdminRole) {
            $highestRole = $superAdminRole;
        } else {
            // Then try owner
            $ownerRole = Role::where('name', 'owner')->where('guard_name', 'web')->first();
            if ($ownerRole) {
                $highestRole = $ownerRole;
            } else {
                // Finally try admin
                $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
                if ($adminRole) {
                    $highestRole = $adminRole;
                }
            }
        }

        if ($highestRole) {
            $user->assignRole($highestRole);
            $this->command->info("Created user: {$user->email} with {$highestRole->name} role (highest available)");
        } else {
            $this->command->error('No suitable role found for user');
        }
    }
}
