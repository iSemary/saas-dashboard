<?php

namespace Database\Seeders\Landlord;

use App\Constants\Landlord\Resources;
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
            [
                'name' => 'landlord',
                'guard_name' => 'api'
            ],
            [
                'name' => 'developer',
                'guard_name' => 'api'
            ],
            [
                'name' => 'marketing',
                'guard_name' => 'api'
            ],
            [
                'name' => 'sales',
                'guard_name' => 'api'
            ],
            [
                'name' => 'viewer',
                'guard_name' => 'api'
            ]
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }

    private function seedPermissions()
    {
        foreach ($this->resources as $resource) {
            foreach ($resource['actions'] as $action) {
                Permission::updateOrCreate(
                    ['name' => "$action.{$resource['name']}", 'guard_name' => 'api'],
                    ['name' => "$action.{$resource['name']}", 'guard_name' => 'api']
                );
            }
        }
    }

    private function seedPermissionsToRoles()
    {
        $roles = ['landlord'];
        $permissions = Permission::all();

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->syncPermissions($permissions);
            }
        }
    }
}
