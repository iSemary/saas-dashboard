<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Creating super admin user...');

        // Create super admin user
        $user = User::updateOrCreate(
            ['email' => 'superadmin@customer1.local'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password123'),
                'customer_id' => 1,
                'email_verified_at' => now(),
            ]
        );

        // Try to assign admin role
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $user->assignRole($adminRole);
            $this->command->info("✅ Super admin user created: {$user->email} with admin role");
        } else {
            // If admin role doesn't exist, try super_admin
            $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
            if ($superAdminRole) {
                $user->assignRole($superAdminRole);
                $this->command->info("✅ Super admin user created: {$user->email} with super_admin role");
            } else {
                $this->command->error('❌ No suitable admin role found. Available roles:');
                Role::all(['name', 'guard_name'])->each(function($r) {
                    $this->command->line("   - {$r->name} ({$r->guard_name})");
                });
            }
        }
    }
}
