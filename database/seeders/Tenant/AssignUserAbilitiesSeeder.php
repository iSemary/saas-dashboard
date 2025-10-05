<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Permission;

class AssignUserAbilitiesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Assigning abilities to super admin user...');

        $user = User::where('email', 'superadmin@customer1.local')->first();
        
        if (!$user) {
            $this->command->error('❌ Super admin user not found');
            return;
        }

        // Define abilities that the user should have
        $abilities = [
            'read.brands',
            'create.brands',
            'update.brands',
            'delete.brands',
            'restore.brands',
            'read.branches',
            'create.branches',
            'update.branches',
            'delete.branches',
            'restore.branches',
        ];

        // Get all permissions
        $permissions = Permission::whereIn('name', $abilities)
            ->where('guard_name', 'web')
            ->get();

        // Assign permissions directly to user
        $user->syncPermissions($permissions);

        $this->command->info("✅ Assigned abilities directly to super admin user:");
        foreach ($abilities as $ability) {
            $this->command->line("   - {$ability}");
        }

        // Verify the abilities
        $this->command->info("\n🔍 Verifying abilities:");
        foreach ($abilities as $ability) {
            if ($user->can($ability)) {
                $this->command->line("   ✅ User can: {$ability}");
            } else {
                $this->command->error("   ❌ User cannot: {$ability}");
            }
        }
    }
}


