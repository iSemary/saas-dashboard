<?php

namespace Modules\Auth\Repositories\Tenant;

use Modules\Auth\Entities\Role;
use Illuminate\Database\Eloquent\Collection;

interface TenantRoleRepositoryInterface
{
    public function datatables();
    /**
     * Get all roles with their relationships
     */
    public function getAllRoles(): Collection;

    /**
     * Get roles with pagination and filters
     */
    public function getRolesWithPagination(array $filters = [], int $perPage = 15);

    /**
     * Create a new role
     */
    public function createRole(array $data): Role;

    /**
     * Update an existing role
     */
    public function updateRole(int $id, array $data): Role;

    /**
     * Delete a role
     */
    public function deleteRole(int $id): bool;

    /**
     * Soft delete a role
     */
    public function softDeleteRole(int $id): bool;

    /**
     * Restore a soft deleted role
     */
    public function restoreRole(int $id): bool;

    /**
     * Find role by ID
     */
    public function findRole(int $id): ?Role;

    /**
     * Get roles assigned to specific permissions
     */
    public function getRolesByPermissionIds(array $permissionIds): Collection;

    /**
     * Assign permissions to a role
     */
    public function assignPermissionsToRole(int $roleId, array $permissionIds): bool;

    /**
     * Remove permissions from a role
     */
    public function removePermissionsFromRole(int $roleId, array $permissionIds): bool;

    /**
     * Sync permissions for a role
     */
    public function syncPermissionsForRole(int $roleId, array $permissionIds): bool;

    /**
     * Get roles with user count
     */
    public function getRolesWithUserCount(): Collection;

    /**
     * Get role statistics
     */
    public function getRoleStatistics(): array;
}
