<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Entities\Role;
use Illuminate\Database\Eloquent\Collection;

interface TenantRoleServiceInterface
{
    /**
     * Get all roles with pagination and filters
     */
    public function getAllRoles(array $filters = [], int $perPage = 15);

    /**
     * Get role by ID
     */
    public function getRoleById(int $id): ?Role;

    /**
     * Create a new role
     */
    public function createRole(array $data): array;

    /**
     * Update an existing role
     */
    public function updateRole(int $id, array $data): array;

    /**
     * Delete a role
     */
    public function deleteRole(int $id): array;

    /**
     * Soft delete a role
     */
    public function softDeleteRole(int $id): array;

    /**
     * Restore a soft deleted role
     */
    public function restoreRole(int $id): array;

    /**
     * Assign permissions to a role
     */
    public function assignPermissionsToRole(int $roleId, array $permissionIds): array;

    /**
     * Remove permissions from a role
     */
    public function removePermissionsFromRole(int $roleId, array $permissionIds): array;

    /**
     * Sync permissions for a role
     */
    public function syncPermissionsForRole(int $roleId, array $permissionIds): array;

    /**
     * Get permissions for a role
     */
    public function getPermissionsForRole(int $roleId): Collection;

    /**
     * Get available permissions for assignment
     */
    public function getAvailablePermissions(): Collection;

    /**
     * Get role statistics
     */
    public function getRoleStatistics(): array;

    /**
     * Validate role data
     */
    public function validateRoleData(array $data, int $roleId = null): array;

    /**
     * Check if role can be deleted
     */
    public function canDeleteRole(int $roleId): array;
}
