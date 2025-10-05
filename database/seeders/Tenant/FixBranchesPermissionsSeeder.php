<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;

class FixBranchesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create branches permissions if they don't exist
        $branchesPermissions = [
            'read.branches',
            'create.branches',
            'update.branches',
            'delete.branches',
            'restore.branches',
        ];

        foreach ($branchesPermissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Assign branches permissions to admin role
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $branchesPermissions = Permission::whereIn('name', $branchesPermissions)
                ->where('guard_name', 'web')
                ->get();
            
            $adminRole->syncPermissions($branchesPermissions);
            $this->command->info("Assigned branches permissions to admin role");
        } else {
            $this->command->error('Admin role not found');
        }
    }
}
