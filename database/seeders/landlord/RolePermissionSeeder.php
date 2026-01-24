<?php

namespace Database\Seeders\landlord;

use App\Constants\Landlord\Resources;
use Illuminate\Database\Seeder;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\PermissionGroup;
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
        $this->seedPermissionGroups();
        $this->seedPermissionsToRoles();
    }

    private function seedRoles()
    {
        $roles = Resources::getRoles();

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
                    ['name' => "$action.{$resource['name']}", 'guard_name' => 'web'],
                    ['name' => "$action.{$resource['name']}", 'guard_name' => 'web']
                );
            }
        }
    }

    private function seedPermissionGroups()
    {
        $permissionGroups = Resources::getPermissionGroups();

        foreach ($permissionGroups as $groupData) {
            $group = PermissionGroup::updateOrCreate(
                ['name' => $groupData['name'], 'guard_name' => $groupData['guard_name'] ?? 'api'],
                [
                    'name' => $groupData['name'],
                    'guard_name' => $groupData['guard_name'] ?? 'api',
                    'description' => $groupData['description'] ?? null,
                ]
            );

            // Sync permissions to the group
            if (isset($groupData['permissions']) && !empty($groupData['permissions'])) {
                $permissionIds = Permission::whereIn('name', $groupData['permissions'])
                    ->pluck('id')
                    ->toArray();
                $group->permissions()->sync($permissionIds);
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
