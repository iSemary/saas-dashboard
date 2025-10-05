<?php

namespace Modules\Auth\Repositories\Tenant;

use Modules\Auth\Entities\Permission;
use Illuminate\Database\Eloquent\Collection;

interface TenantPermissionRepositoryInterface
{
    public function datatables();
    /**
     * Get all permissions with their relationships
     */
    public function getAllPermissions(): Collection;

    /**
     * Get permissions with pagination and filters
     */
    public function getPermissionsWithPagination(array $filters = [], int $perPage = 15);

    /**
     * Create a new permission
     */
    public function createPermission(array $data): Permission;

    /**
     * Update an existing permission
     */
    public function updatePermission(int $id, array $data): Permission;

    /**
     * Delete a permission
     */
    public function deletePermission(int $id): bool;

    /**
     * Soft delete a permission
     */
    public function softDeletePermission(int $id): bool;

    /**
     * Restore a soft deleted permission
     */
    public function restorePermission(int $id): bool;

    /**
     * Find permission by ID
     */
    public function findPermission(int $id): ?Permission;

    /**
     * Get permissions assigned to specific roles
     */
    public function getPermissionsByRoleIds(array $roleIds): Collection;

    /**
     * Get permissions grouped by resource
     */
    public function getPermissionsGroupedByResource(): Collection;

    /**
     * Get permissions by action (view, create, update, delete)
     */
    public function getPermissionsByAction(string $action): Collection;

    /**
     * Bulk create permissions for a-resource and actions
     */
    public function bulkCreateResourcePermissions(string $resource, array $actions = ['view', 'create', 'update', 'delete']): Collection;

    /**
     * Assign permission to roles
     */
    public function assignPermissionToRoles(int $permissionId, array $roleIds): bool;

    /**
     * Remove permission from roles
     */
    public function removePermissionFromRoles(int $permissionId, array $roleIds): bool;

    /**
     * Get permission statistics
     */
    public function getPermissionStatistics(): array;
}
