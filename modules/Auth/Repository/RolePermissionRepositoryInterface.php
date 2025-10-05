<?php

namespace Modules\Auth\Repository;

use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;
use Illuminate\Database\Eloquent\Collection;

interface RolePermissionRepositoryInterface
{
    public function getAllRoles(): Collection;
    public function createRole(array $data): Role;
    public function updateRole(int $id, array $data): Role;
    public function deleteRole(int $id): bool;
    public function findRole(int $id): Role|null;
    public function getAllPermissions(): Collection;
    public function createPermission(array $data): Permission;
    public function updatePermission(int $id, array $data): Permission;
    public function deletePermission(int $id): bool;
    public function findPermission(int $id): Permission|null;
    public function getPermissionsByRole(Role $role): Collection;
    public function assignPermissionsToRole(Role $role, array $permissionIds): bool;
    public function getStatistics(): array;
}
