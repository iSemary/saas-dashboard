<?php

namespace Database\Seeders\tenant;

use App\Constants\Tenant\Resources;
use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;

class RolePermissionSeeder extends Seeder
{
    private $resources;

    public function __construct()
    {
        $this->resources = Resources::getResources();
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->seedPermissionsToRoles();
    }

    private function seedRoles()
    {
        $roles = [
            // API Roles
            [
                'name' => 'owner',
                'guard_name' => 'api'
            ],
            [
                'name' => 'super_admin',
                'guard_name' => 'api'
            ],
            [
                'name' => 'admin',
                'guard_name' => 'api'
            ],
            [
                'name' => 'viewer',
                'guard_name' => 'api'
            ],
            // Web Roles for Tenant Dashboard
            [
                'name' => 'owner',
                'guard_name' => 'web'
            ],
            [
                'name' => 'admin',
                'guard_name' => 'web'
            ],
            [
                'name' => 'manager',
                'guard_name' => 'web'
            ],
            [
                'name' => 'employee',
                'guard_name' => 'web'
            ],
            [
                'name' => 'viewer',
                'guard_name' => 'web'
            ]
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
        $defaultActions = ['view', 'create', 'update', 'delete'];

        // Flatten resources and extract resource names with their actions
        $flattenedResources = $this->flattenResources($this->resources);

        // Seed API permissions
        foreach ($flattenedResources as $resourceName => $actions) {
            $actions = !empty($actions) ? $actions : $defaultActions;

            foreach ($actions as $action) {
                Permission::updateOrCreate(
                    ['name' => "$action.$resourceName", 'guard_name' => 'api'],
                    ['name' => "$action.$resourceName", 'guard_name' => 'api']
                );
            }
        }

        // Seed Web permissions for tenant dashboard
        $webResources = array_merge($flattenedResources, [
            'roles' => $defaultActions,
            'permissions' => $defaultActions,
            'users' => $defaultActions,
        ]);

        foreach ($webResources as $resourceName => $actions) {
            $actions = !empty($actions) ? $actions : $defaultActions;

            foreach ($actions as $action) {
                Permission::updateOrCreate(
                    ['name' => "$action.$resourceName", 'guard_name' => 'web'],
                    ['name' => "$action.$resourceName", 'guard_name' => 'web']
                );
            }
        }

        // Additional specific web permissions
        $specificPermissions = [
            'manage.roles',
            'manage.permissions',
            'manage.users',
            'assign.roles',
            'assign.permissions',
            'view.statistics',
            'view.dashboard',
            'bulk.operations'
        ];

        foreach ($specificPermissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
        }
    }

    private function flattenResources(array $resources): array
    {
        $flattened = [];

        foreach ($resources as $key => $value) {
            if (is_string($key) && is_array($value)) {
                // Check if this is a module with nested resources
                $firstKey = array_key_first($value);
                if (is_string($firstKey) && is_array($value[$firstKey])) {
                    // This is a module with nested resources (e.g., 'crm' => ['leads' => [...]])
                    foreach ($value as $resourceName => $actions) {
                        $flattened[$resourceName] = is_array($actions) ? $actions : [];
                    }
                } else {
                    // This is a flat resource with actions (e.g., 'users' => ['read', ...])
                    $flattened[$key] = $value;
                }
            }
        }

        return $flattened;
    }

    private function seedPermissionsToRoles()
    {
        // API Permissions
        $apiPermissions = Permission::where('guard_name', 'api')->get();
        $apiRoles = ['owner', 'super_admin'];

        foreach ($apiRoles as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'api')->first();
            if ($role) {
                $role->syncPermissions($apiPermissions);
            }
        }

        // Web Permissions - Owner gets all permissions
        $webPermissions = Permission::where('guard_name', 'web')->get();
        $ownerRole = Role::where('name', 'owner')->where('guard_name', 'web')->first();
        if ($ownerRole) {
            $ownerRole->syncPermissions($webPermissions);
        }

        // Admin gets most permissions except managing roles and permissions
        $adminPermissions = Permission::where('guard_name', 'web')
            ->where('name', 'not like', 'delete.roles')
            ->where('name', 'not like', 'delete.permissions')
            ->get();
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->syncPermissions($adminPermissions);
        }

        // Manager gets view, create, update for most resources
        $managerPermissions = Permission::where('guard_name', 'web')
            ->where(function ($query) {
                $query->where('name', 'like', 'view.%')
                    ->orWhere('name', 'like', 'create.%')
                    ->orWhere('name', 'like', 'update.%');
            })
            ->where('name', 'not like', '%.roles')
            ->where('name', 'not like', '%.permissions')
            ->get();
        $managerRole = Role::where('name', 'manager')->where('guard_name', 'web')->first();
        if ($managerRole) {
            $managerRole->syncPermissions($managerPermissions);
        }

        // Employee gets view and create for limited resources
        $employeePermissions = Permission::where('guard_name', 'web')
            ->where(function ($query) {
                $query->where('name', 'like', 'view.%')
                    ->orWhere('name', 'like', 'create.%');
            })
            ->where('name', 'not like', '%.users')
            ->where('name', 'not like', '%.roles')
            ->where('name', 'not like', '%.permissions')
            ->get();
        $employeeRole = Role::where('name', 'employee')->where('guard_name', 'web')->first();
        if ($employeeRole) {
            $employeeRole->syncPermissions($employeePermissions);
        }

        // Viewer gets only view permissions
        $viewerPermissions = Permission::where('guard_name', 'web')
            ->where('name', 'like', 'view.%')
            ->where('name', '!=', 'view.roles')
            ->where('name', '!=', 'view.permissions')
            ->get();
        $viewerRole = Role::where('name', 'viewer')->where('guard_name', 'web')->first();
        if ($viewerRole) {
            $viewerRole->syncPermissions($viewerPermissions);
        }
    }
}
