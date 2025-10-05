<?php

namespace Modules\Auth\Services;

use Modules\Auth\Entities\User;
use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Repository\UserManagementRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserManagementService
{
    protected UserManagementRepositoryInterface $userManagementRepository;

    public function __construct(UserManagementRepositoryInterface $userManagementRepository)
    {
        $this->userManagementRepository = $userManagementRepository;
    }

    /**
     * Get paginated users with filters
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15, array $orderBy = []): LengthAwarePaginator
    {
        return $this->userManagementRepository->getUsersWithFilters($filters, $perPage, $orderBy);
    }

    /**
     * Create new user
     */
    public function createUser(array $userData): User
    {
        DB::beginTransaction();
        
        try 
        {
            // Hash password if provided
            if (isset($userData['password'])) 
            {
                $userData['password'] = Hash::make($userData['password']);
            }

            // Create user
            $user = $this->userManagementRepository->createUser($userData);

            // Assign roles if provided
            if (isset($userData['roles'])) 
            {
                $this->assignRoles($user->id, $userData['roles']);
            }

            // Assign permissions if provided
            if (isset($userData['permissions'])) 
            {
                $this->assignDirectPermissions($user->id, $userData['permissions']);
            }

            // Set user meta if provided
            if (isset($userData['meta'])) 
            {
                $this->setUserMeta($user->id, $userData['meta']);
            }

            DB::commit();
            
            return $user->load(['roles', 'permissions']);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update user
     */
    public function updateUser(int $userId, array $userData): User
    {
        DB::beginTransaction();
        
        try 
        {
            $user = $this->userManagementRepository->findUser($userId);
            if (!$user) 
            {
                throw new \Exception('User not found');
            }

            // Hash password if provided and not empty
            if (isset($userData['password']) && !empty($userData['password'])) 
            {
                $userData['password'] = Hash::make($userData['password']);
            } 
            else 
            {
                unset($userData['password']); // Remove empty password
            }

            // Update basic user data
            $this->userManagementRepository->updateUser($userId, $userId);

            // Update roles if provided
            if (isset($userData['roles'])) 
            {
                $user->syncRoles($userData['roles']);
            }

            // Update permissions if provided
            if (isset($userData['permissions'])) 
            {
                $user->syncPermissions($userData['permissions']);
            }

            // Update user meta if provided
            if (isset($userData['meta'])) 
            {
                $this->setUserMeta($userId, $userData['meta']);
            }

            DB::commit();
            
            return $user->fresh(['roles', 'permissions']);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser(int $userId): bool
    {
        return $this->userManagementRepository->deleteUser($userId);
    }

    /**
     * Assign roles to user
     */
    public function assignRoles(int $userId, array $roleIds): bool
    {
        $user = $this->userManagementRepository->findUser($userId);
        if (!$user) 
        {
            throw new \Exception('User not found');
        }

        $user->assignRole($roleIds);
        
        return true;
    }

    /**
     * Remove roles from user
     */
    public function removeRoles(int $userId, array $roleIds): bool
    {
        $user = $this->userManagementRepository->findUser($userId);
        if (!$user) 
        {
            throw new \Exception('User not found');
        }

        $user->removeRole($roleIds);
        
        return true;
    }

    /**
     * Assign direct permissions to user
     */
    public function assignDirectPermissions(int $userId, array $permissionIds): bool
    {
        $user = $this->userManagementRepository->findUser($userId);
        if (!$user) 
        {
            throw new \Exception('User not found');
        }

        $user->givePermissionTo($permissionIds);
        
        return true;
    }

    /**
     * Set user meta data
     */
    public function setUserMeta(int $userId, array $metaData): bool
    {
        return $this->userManagementRepository->setUserMeta($userId, $metaData);
    }

    /**
     * Get user details with roles and permissions
     */
    public function getUserDetails(int $userId): array|null
    {
        $user = $this->userManagementRepository->findUserWithRelations($userId);
        
        if (!$user) 
        {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'status' => $user->status ?? 'active',
            'country_id' => $user->country_id,
            'language_id' => $user->language_id,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'roles' => $user->roles->toArray(),
            'permissions' => $user->getAllPermissions()->toArray(),
            'meta' => $user->userMeta()->pluck('value', 'meta_key')->toArray(),
            'login_attempts' => $user->loginAttempts ?? [],
            'last_login_at' => $user->last_login_at,
        ];
    }

    /**
     * Get user roles
     */
    public function getUserRoles(int $userId): array
    {
        $user = $this->userManagementRepository->findUserWithRoles($userId);
        return $user ? $user->roles->toArray() : [];
    }

    /**
     * Get user role permissions
     */
    public function getUserRolePermissions(int $userId): array
    {
        $user = $this->userManagementRepository->findUserWithRoles($userId);
        
        if (!$user) 
        {
            return [];
        }

        $permissions = [];
        foreach ($user->roles as $role) 
        {
            $permissions = array_merge($permissions, $role->permissions->toArray());
        }

        return $permissions;
    }

    /**
     * Get user direct permissions
     */
    public function getUserDirectPermissions(int $userId): array
    {
        $user = $this->userManagementRepository->findUserWithDirectPermissions($userId);
        return $user ? $user->permissions->toArray() : [];
    }

    /**
     * Get all user permissions (roles + direct)
     */
    public function getUserAllPermissions(int $userId): array
    {
        $user = $this->userManagementRepository->findUserWithRolesAndPermissions($userId);
        return $user ? $user->getAllPermissions()->toArray() : [];
    }

    /**
     * Toggle user status
     */
    public function toggleUserStatus(int $userId): string
    {
        $user = $this->userManagementRepository->findUser($userId);
        if (!$user) 
        {
            throw new \Exception('User not found');
        }

        $currentStatus = $user->status ?? 'active';
        $newStatus = $currentStatus === 'active' ? 'inactive' : 'active';

        $user->update(['status' => $newStatus]);

        return $newStatus;
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        return $this->userManagementRepository->getUserStatistics();
    }

    /**
     * Search users
     */
    public function searchUsers(string $query, int $limit = 10): array
    {
        return $this->userManagementRepository->searchUsers($query, $limit);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $roleName): array
    {
        return $this->userManagementRepository->getUsersByRole($roleName);
    }

    /**
     * Bulk update users
     */
    public function bulkUpdateUsers(array $userIds, array $updateData): array
    {
        $results = [];
        
        DB::beginTransaction();
        
        try 
        {
            foreach ($userIds as $userId) 
            {
                try 
                {
                    $this->updateUser($userId, $updateData);
                    $results[] = ['id' => $userId, 'status' => 'success'];
                } 
                catch (\Exception $e) 
                {
                    $results[] = ['id' => $userId, 'status' => 'failed', 'error' => $e->getMessage()];
                }
            }

            DB::commit();
            
            return $results;
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDeleteUsers(array $userIds): array
    {
        $results = [];
        
        DB::beginTransaction();
        
        try 
        {
            foreach ($userIds as $userId) 
            {
                try 
                {
                    $this->deleteUser($userId);
                    $results[] = ['id' => $userId, 'status' => 'success'];
                } 
                catch (\Exception $e) 
                {
                    $results[] = ['id' => $userId, 'status' => 'failed', 'error' => $e->getMessage()];
                }
            }

            DB::commit();
            
            return $results;
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            throw $e;
        }
    }
}
