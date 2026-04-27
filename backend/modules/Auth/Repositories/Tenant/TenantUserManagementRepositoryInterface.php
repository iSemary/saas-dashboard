<?php

namespace Modules\Auth\Repositories\Tenant;

use Modules\Auth\Entities\User;
use Illuminate\Database\Eloquent\Collection;

interface TenantUserManagementRepositoryInterface
{
    /**
     * Get all users with their relationships
     */
    public function getAllUsers(): Collection;

    /**
     * Get users with pagination and filters
     */
    public function getUsersWithPagination(array $filters = [], int $perPage = 15);

    /**
     * Create a new user
     */
    public function createUser(array $data): User;

    /**
     * Update an existing user
     */
    public function updateUser(int $id, array $data): User;

    /**
     * Delete a user
     */
    public function deleteUser(int $id): bool;

    /**
     * Soft delete a user
     */
    public function softDeleteUser(int $id): bool;

    /**
     * Restore a soft deleted user
     */
    public function restoreUser(int $id): bool;

    /**
     * Find user by ID
     */
    public function findUser(int $id): ?User;

    /**
     * Assign roles to a user
     */
    public function assignRolesToUser(int $userId, array $roleIds): bool;

    /**
     * Remove roles from a user
     */
    public function removeRolesFromUser(int $userId, array $roleIds): bool;

    /**
     * Sync roles for a user
     */
    public function syncRolesForUser(int $userId, array $roleIds): bool;

    /**
     * Assign permissions to a user
     */
    public function assignPermissionsToUser(int $userId, array $permissionIds): bool;

    /**
     * Remove specific permissions from a user
     */
    public function removePermissionsFromUser(int $userId, array $permissionIds): bool;

    /**
     * Sync permissions for a user
     */
    public function syncPermissionsForUser(int $userId, array $permissionIds): bool;

    /**
     * Get users by role
     */
    public function getUsersByRole(int $roleId): Collection;

    /**
     * Get users by role name
     */
    public function getUsersByRoleName(string $roleName): Collection;

    /**
     * Get active users
     */
    public function getActiveUsers(): Collection;

    /**
     * Get inactive users
     */
    public function getInactiveUsers(): Collection;

    /**
     * Activate user account
     */
    public function activateUser(int $userId): bool;

    /**
     * Deactivate user account
     */
    public function deactivateUser(int $userId): bool;

    /**
     * Bulk activate users
     */
    public function bulkActivateUsers(array $userIds): bool;

    /**
     * Bulk deactivate users
     */
    public function bulkDeactivateUsers(array $userIds): bool;

    /**
     * Bulk delete users
     */
    public function bulkDeleteUsers(array $userIds): bool;

    /**
     * Get user statistics
     */
    public function getUserStatistics(): array;

    /**
     * Search users by name, email, or username
     */
    public function searchUsers(string $query): Collection;

    /**
     * Get users created in date range
     */
    public function getUsersCreatedBetween(string $startDate, string $endDate): Collection;
}
