<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Illuminate\Support\Facades\Auth;

class TestUserAccessSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧪 Testing user access to brands and branches...');

        $user = User::where('email', 'superadmin@customer1.local')->first();
        
        if (!$user) {
            $this->command->error('❌ Super admin user not found');
            return;
        }

        // Simulate authentication
        Auth::login($user);
        
        $this->command->info("✅ Logged in user: {$user->name} ({$user->email})");
        
        // Test abilities
        $abilities = [
            'read.brands',
            'read.branches',
            'create.brands',
            'create.branches',
        ];

        $this->command->info("\n🔍 Testing abilities:");
        foreach ($abilities as $ability) {
            if ($user->can($ability)) {
                $this->command->line("   ✅ User can: {$ability}");
            } else {
                $this->command->error("   ❌ User cannot: {$ability}");
            }
        }

        // Test role
        $this->command->info("\n👤 User roles:");
        foreach ($user->roles as $role) {
            $this->command->line("   - {$role->name} ({$role->guard_name})");
        }

        // Test permissions count
        $permissionsCount = $user->getAllPermissions()->count();
        $this->command->info("\n📊 Total permissions: {$permissionsCount}");

        // Test specific permissions
        $brandPermissions = $user->getAllPermissions()
            ->whereIn('name', ['read.brands', 'create.brands', 'update.brands', 'delete.brands'])
            ->pluck('name')
            ->toArray();
        
        $this->command->info("   Brands permissions: " . implode(', ', $brandPermissions));

        $branchPermissions = $user->getAllPermissions()
            ->whereIn('name', ['read.branches', 'create.branches', 'update.branches', 'delete.branches'])
            ->pluck('name')
            ->toArray();
        
        $this->command->info("   Branches permissions: " . implode(', ', $branchPermissions));

        Auth::logout();
        $this->command->info("\n✅ Logged out user");
    }
}


