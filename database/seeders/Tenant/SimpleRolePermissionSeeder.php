<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class SimpleRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->assignPermissionsToRoles();
    }

    private function seedRoles()
    {
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'manager', 'guard_name' => 'web'],
            ['name' => 'employee', 'guard_name' => 'web'],
            ['name' => 'viewer', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name'], 'guard_name' => $role['guard_name']],
                $role
            );
        }
    }

    private function seedPermissions()
    {
        $permissions = [
            // Branches permissions
            'read.branches',
            'create.branches',
            'update.branches',
            'delete.branches',
            'restore.branches',
            
            // Users permissions
            'read.users',
            'create.users',
            'update.users',
            'delete.users',
            
            // Roles permissions
            'read.roles',
            'create.roles',
            'update.roles',
            'delete.roles',
            
            // Permissions permissions
            'read.permissions',
            'create.permissions',
            'update.permissions',
            'delete.permissions',
            
            // Dashboard permissions
            'read.dashboard',
            'view.statistics',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }
    }

    private function assignPermissionsToRoles()
    {
        // Admin gets all permissions
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());
        }

        // Manager gets most permissions except user management
        $managerRole = Role::where('name', 'manager')->where('guard_name', 'web')->first();
        if ($managerRole) {
            $managerPermissions = Permission::where('guard_name', 'web')
                ->whereNotIn('name', ['create.users', 'update.users', 'delete.users', 'create.roles', 'update.roles', 'delete.roles'])
                ->get();
            $managerRole->syncPermissions($managerPermissions);
        }

        // Employee gets basic permissions
        $employeeRole = Role::where('name', 'employee')->where('guard_name', 'web')->first();
        if ($employeeRole) {
            $employeePermissions = Permission::where('guard_name', 'web')
                ->whereIn('name', ['read.branches', 'read.users', 'read.dashboard', 'view.statistics'])
                ->get();
            $employeeRole->syncPermissions($employeePermissions);
        }

        // Viewer gets read-only permissions
        $viewerRole = Role::where('name', 'viewer')->where('guard_name', 'web')->first();
        if ($viewerRole) {
            $viewerPermissions = Permission::where('guard_name', 'web')
                ->whereIn('name', ['read.branches', 'read.users', 'read.dashboard', 'view.statistics'])
                ->get();
            $viewerRole->syncPermissions($viewerPermissions);
        }
    }
}
