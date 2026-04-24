<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Entities\User;
use Illuminate\Database\Eloquent\Collection;

interface TenantUserManagementServiceInterface
{
    public function getDataTables();
    /**
     * Get all users with pagination and filters
     */
    public function getAllUsers(array $filters = [], int $perPage = 15);

    /**
     * Get user by ID
     */
    public function getUserById(int $id): ?User;

    /**
     * Create a new user
     */
    public function createUser(array $data): array;

    /**
     * Update an existing user
     */
    public function updateUser(int $id, array $data): array;

    /**
     * Delete a user
     */
    public function deleteUser(int $id): array;

    /**
     * Soft delete a user
     */
    public function softDeleteUser(int $id): array;

    /**
     * Restore a soft deleted user
     */
    public function restoreUser(int $id): array;

    /**
     * Assign roles to a user
     */
    public function assignRolesToUser(int $userId, array $roleIds): array;

    /**
     * Remove roles from a user
     */
    public function removeRolesFromUser(int $userId, array $roleIds): array;

    /**
     * Sync roles for a user
     */
    public function syncRolesForUser(int $userId, array $roleIds): array;

    /**
     * Assign permissions to a user
     */
    public function assignPermissionsToUser(int $userId, array $permissionIds): array;

    /**
     * Remove permissions from a user
     */
    public function removePermissionsFromUser(int $userId, array $permissionIds): array;

    /**
     * Sync permissions for a user
     */
    public function syncPermissionsForUser(int $userId, array $permissionIds): array;

    /**
     * Get users by role
     */
    public function getUsersByRole(int $roleId): Collection;

    /**
     * Get users by role name
     */
    public function getUsersByRoleName(string $roleName): Collection;

    /**
     * Activate user account
     */
    public function activateUser(int $userId): array;

    /**
     * Deactivate user account
     */
    public function deactivateUser(int $userId): array;

    /**
     * Bulk activate users
     */
    public function bulkActivateUsers(array $userIds): array;

    /**
     * Bulk deactivate users
     */
    public function bulkDeactivateUsers(array $userIds): array;

    /**
     * Bulk delete users
     */
    public function bulkDeleteUsers(array $userIds): array;

    /**
     * Get user statistics
     */
    public function getUserStatistics(): array;

    /**
     * Reset user password
     */
    public function resetUserPassword(int $userId, string $newPassword): array;

    /**
     * Validate user data
     */
    public function validateUserData(array $data, int $userId = null): array;

    /**
     * Check if user can be deleted
     */
    public function canDeleteUser(int $userId): array;

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(int $userId): array;
}
