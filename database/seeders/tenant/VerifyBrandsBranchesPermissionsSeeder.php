<?php

namespace Database\Seeders\tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;

class VerifyBrandsBranchesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔍 Verifying brands and branches permissions for super admin...');

        $user = User::where('email', 'superadmin@customer1.local')->first();
        
        if ($user) {
            $this->command->info("✅ User found: {$user->name} ({$user->email})");
            
            // Check user roles
            $userRoles = $user->roles->pluck('name')->toArray();
            $this->command->info("   Roles: " . implode(', ', $userRoles));
            
            // Check brands permissions
            $brandsPermissions = $user->getAllPermissions()
                ->whereIn('name', ['read.brands', 'create.brands', 'update.brands', 'delete.brands', 'restore.brands'])
                ->pluck('name')
                ->toArray();
            
            $this->command->info("   Brands permissions: " . implode(', ', $brandsPermissions));
            
            // Check branches permissions
            $branchesPermissions = $user->getAllPermissions()
                ->whereIn('name', ['read.branches', 'create.branches', 'update.branches', 'delete.branches', 'restore.branches'])
                ->pluck('name')
                ->toArray();
            
            $this->command->info("   Branches permissions: " . implode(', ', $branchesPermissions));
            
            // Check if user can access brands
            if ($user->can('read.brands')) {
                $this->command->info("   ✅ User can read brands");
            } else {
                $this->command->error("   ❌ User cannot read brands");
            }
            
            // Check if user can access branches
            if ($user->can('read.branches')) {
                $this->command->info("   ✅ User can read branches");
            } else {
                $this->command->error("   ❌ User cannot read branches");
            }
            
        } else {
            $this->command->error('❌ Super admin user not found');
        }
    }
}


