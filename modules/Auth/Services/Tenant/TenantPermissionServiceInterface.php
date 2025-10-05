<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Entities\Permission;
use Illuminate\Database\Eloquent\Collection;

interface TenantPermissionServiceInterface
{
    public function getDataTables();
    /**
     * Get all permissions with pagination and filters
     */
    public function getAllPermissions(array $filters = [], int $perPage = 15);

    /**
     * Get permission by ID
     */
    public function getPermissionById(int $id): ?Permission;

    /**
     * Create a new permission
     */
    public function createPermission(array $data): array;

    /**
     * Update an existing permission
     */
    public function updatePermission(int $id, array $data): array;

    /**
     * Delete a permission
     */
    public function deletePermission(int $id): array;

    /**
     * Soft delete a permission
     */
    public function softDeletePermission(int $id): array;

    /**
     * Restore a soft deleted permission
     */
    public function restorePermission(int $id): array;

    /**
     * Assign permission to roles
     */
    public function assignPermissionToRoles(int $permissionId, array $roleIds): array;

    /**
     * Remove permission from roles
     */
    public function removePermissionFromRoles(int $permissionId, array $roleIds): array;

    /**
     * Bulk create resource permissions
     */
    public function bulkCreateResourcePermissions(string $resource, array $actions = ['view', 'create', 'update', 'delete']): array;

    /**
     * Get permissions grouped by resource
     */
    public function getPermissionsGroupedByResource(): Collection;

    /**
     * Get permissions by action
     */
    public function getPermissionsByAction(string $action): Collection;

    /**
     * Get permission statistics
     */
    public function getPermissionStatistics(): array;

    /**
     * Validate permission data
     */
    public function validatePermissionData(array $data, int $permissionId = null): array;

    /**
     * Check if permission can be deleted
     */
    public function canDeletePermission(int $permissionId): array;
}
