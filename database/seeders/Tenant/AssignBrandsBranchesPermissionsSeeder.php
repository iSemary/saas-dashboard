<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class AssignBrandsBranchesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏢 Assigning brands and branches permissions to admin role...');

        // Define brands and branches permissions
        $permissions = [
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

        // Create permissions if they don't exist
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        // Get admin role
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        
        if ($adminRole) {
            // Assign all brands and branches permissions to admin role
            $permissionObjects = Permission::whereIn('name', $permissions)
                ->where('guard_name', 'web')
                ->get();
            
            $adminRole->syncPermissions($permissionObjects);
            $this->command->info("✅ Assigned brands and branches permissions to admin role");
            
            $this->command->info("✅ Admin role now has brands and branches permissions:");
            foreach ($permissions as $permission) {
                $this->command->line("   - {$permission}");
            }
        } else {
            $this->command->error('❌ Admin role not found');
        }
    }
}


