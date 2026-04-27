<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Entities\User;
use Modules\Auth\Repositories\Tenant\TenantUserManagementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantUserManagementService implements TenantUserManagementServiceInterface
{
    protected $userRepository;

    public function __construct(TenantUserManagementRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        try
        {
            return $this->userRepository->getUsersWithPagination($filters, $perPage);
        }
        catch (\Exception $e)
        {
            throw new \Exception(@translate('error_fetching_users') . ': ' . $e->getMessage());
        }
    }

    public function getUserById(int $id): ?User
    {
        try
        {
            return $this->userRepository->findUser($id);
        }
        catch (\Exception $e)
        {
            throw new \Exception(@translate('error_fetching_user') . ': ' . $e->getMessage());
        }
    }

    public function createUser(array $data): array
    {
        // Validate data
        $validation = $this->validateUserData($data);
        if (!$validation['valid'])
        {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'] ?? []
            ];
        }

        try
        {
            DB::beginTransaction();

            $user = $this->userRepository->createUser($data);

            DB::commit();

            return [
                'success' => true,
                'message' => @translate('user_created_successfully'),
                'data' => $user
            ];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return [
                'success' => false,
                'message' => @translate('error_creating_user') . ': ' . $e->getMessage()
            ];
        }
    }

    public function updateUser(int $id, array $data): array
    {
        // Validate data
        $validation = $this->validateUserData($data, $id);
        if (!$validation['valid'])
        {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'] ?? []
            ];
        }

        try
        {
            DB::beginTransaction();

            $user = $this->userRepository->updateUser($id, $data);

            DB::commit();

            return [
                'success' => true,
                'message' => @translate('user_updated_successfully'),
                'data' => $user
            ];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return [
                'success' => false,
                'message' => @translate('error_updating_user') . ': ' . $e->getMessage()
            ];
        }
    }

    public function deleteUser(int $id): array
    {
        // Check if user can be deleted
        $canDelete = $this->canDeleteUser($id);
        if (!$canDelete['can_delete'])
        {
            return [
                'success' => false,
                'message' => $canDelete['message']
            ];
        }

        try
        {
            DB::beginTransaction();

            $result = $this->userRepository->deleteUser($id);

            DB::commit();

            return [
                'success' => true,
                'message' => @translate('user_deleted_successfully'),
                'data' => ['deleted' => $result]
            ];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return [
                'success' => false,
                'message' => @translate('error_deleting_user') . ': ' . $e->getMessage()
            ];
        }
    }

    public function softDeleteUser(int $id): array
    {
        try
        {
            $result = $this->userRepository->softDeleteUser($id);

            return [
                'success' => true,
                'message' => @translate('user_soft_deleted_successfully'),
                'data' => ['deleted' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_deleting_user') . ': ' . $e->getMessage()
            ];
        }
    }

    public function restoreUser(int $id): array
    {
        try
        {
            $result = $this->userRepository->restoreUser($id);

            return [
                'success' => true,
                'message' => @translate('user_restored_successfully'),
                'data' => ['restored' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_restoring_user') . ': ' . $e->getMessage()
            ];
        }
    }

    public function assignRolesToUser(int $userId, array $roleIds): array
    {
        try
        {
            $result = $this->userRepository->assignRolesToUser($userId, $roleIds);

            return [
                'success' => true,
                'message' => @translate('roles_assigned_to_user_successfully'),
                'data' => ['assigned' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_assigning_roles') . ': ' . $e->getMessage()
            ];
        }
    }

    public function removeRolesFromUser(int $userId, array $roleIds): array
    {
        try
        {
            $result = $this->userRepository->removeRolesFromUser($userId, $roleIds);

            return [
                'success' => true,
                'message' => @translate('roles_removed_from_user_successfully'),
                'data' => ['removed' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_removing_roles') . ': ' . $e->getMessage()
            ];
        }
    }

    public function syncRolesForUser(int $userId, array $roleIds): array
    {
        try
        {
            $result = $this->userRepository->syncRolesForUser($userId, $roleIds);

            return [
                'success' => true,
                'message' => @translate('user_roles_synced_successfully'),
                'data' => ['synced' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_syncing_roles') . ': ' . $e->getMessage()
            ];
        }
    }

    public function assignPermissionsToUser(int $userId, array $permissionIds): array
    {
        try
        {
            $result = $this->userRepository->assignPermissionsToUser($userId, $permissionIds);

            return [
                'success' => true,
                'message' => @translate('permissions_assigned_to_user_successfully'),
                'data' => ['assigned' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_assigning_permissions') . ': ' . $e->getMessage()
            ];
        }
    }

    public function removePermissionsFromUser(int $userId, array $permissionIds): array
    {
        try
        {
            $result = $this->userRepository->removePermissionsFromUser($userId, $permissionIds);

            return [
                'success' => true,
                'message' => @translate('permissions_removed_from_user_successfully'),
                'data' => ['removed' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_removing_permissions') . ': ' . $e->getMessage()
            ];
        }
    }

    public function syncPermissionsForUser(int $userId, array $permissionIds): array
    {
        try
        {
            $result = $this->userRepository->syncPermissionsForUser($userId, $permissionIds);

            return [
                'success' => true,
                'message' => @translate('user_permissions_synced_successfully'),
                'data' => ['synced' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_syncing_permissions') . ': ' . $e->getMessage()
            ];
        }
    }

    public function getUsersByRole(int $roleId): Collection
    {
        try
        {
            return $this->userRepository->getUsersByRole($roleId);
        }
        catch (\Exception $e)
        {
            throw new \Exception(@translate('error_fetching_users_by_role') . ': ' . $e->getMessage());
        }
    }

    public function getUsersByRoleName(string $roleName): Collection
    {
        try
        {
            return $this->userRepository->getUsersByRoleName($roleName);
        }
        catch (\Exception $e)
        {
            throw new \Exception(@translate('error_fetching_users_by_role_name') . ': ' . $e->getMessage());
        }
    }

    public function activateUser(int $userId): array
    {
        try
        {
            $result = $this->userRepository->activateUser($userId);

            return [
                'success' => true,
                'message' => @translate('user_activated_successfully'),
                'data' => ['activated' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_activating_user') . ': ' . $e->getMessage()
            ];
        }
    }

    public function deactivateUser(int $userId): array
    {
        try
        {
            $result = $this->userRepository->deactivateUser($userId);

            return [
                'success' => true,
                'message' => @translate('user_deactivated_successfully'),
                'data' => ['deactivated' => $result]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_deactivating_user') . ': ' . $e->getMessage()
            ];
        }
    }

    public function bulkActivateUsers(array $userIds): array
    {
        try
        {
            DB::beginTransaction();

            $result = $this->userRepository->bulkActivateUsers($userIds);

            DB::commit();

            return [
                'success' => true,
                'message' => @translate('users_activated_successfully'),
                'data' => ['activated' => $result, 'count' => count($userIds)]
            ];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return [
                'success' => false,
                'message' => @translate('error_activating_users') . ': ' . $e->getMessage()
            ];
        }
    }

    public function bulkDeactivateUsers(array $userIds): array
    {
        try
        {
            DB::beginTransaction();

            $result = $this->userRepository->bulkDeactivateUsers($userIds);

            DB::commit();

            return [
                'success' => true,
                'message' => @translate('users_deactivated_successfully'),
                'data' => ['deactivated' => $result, 'count' => count($userIds)]
            ];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return [
                'success' => false,
                'message' => @translate('error_deactivating_users') . ': ' . $e->getMessage()
            ];
        }
    }

    public function bulkDeleteUsers(array $userIds): array
    {
        try
        {
            DB::beginTransaction();

            $result = $this->userRepository->bulkDeleteUsers($userIds);

            DB::commit();

            return [
                'success' => true,
                'message' => @translate('users_deleted_successfully'),
                'data' => ['deleted' => $result, 'count' => count($userIds)]
            ];
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return [
                'success' => false,
                'message' => @translate('error_deleting_users') . ': ' . $e->getMessage()
            ];
        }
    }

    public function getUserStatistics(): array
    {
        try
        {
            return $this->userRepository->getUserStatistics();
        }
        catch (\Exception $e)
        {
            throw new \Exception(@translate('error_fetching_user_statistics') . ': ' . $e->getMessage());
        }
    }

    public function resetUserPassword(int $userId, string $newPassword): array
    {
        try
        {
            $user = $this->userRepository->findUser($userId);

            if (!$user)
            {
                return [
                    'success' => false,
                    'message' => @translate('user_not_found')
                ];
            }

            $data = [
                'password' => Hash::make($newPassword)
            ];

            $this->userRepository->updateUser($userId, $data);

            return [
                'success' => true,
                'message' => @translate('password_reset_successfully')
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_resetting_password') . ': ' . $e->getMessage()
            ];
        }
    }

    public function validateUserData(array $data, int $userId = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'username' => 'nullable|string|max:64',
            'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8',
            'country_id' => 'nullable|exists:countries,id',
            'language_id' => 'nullable|exists:languages,id',
            'factor_authenticate' => 'nullable|boolean',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        // Update uniqueness rules for editing
        if ($userId)
        {
            $rules['email'] .= '|unique:users,email,' . $userId;
            if (isset($data['username']))
            {
                $rules['username'] .= '|unique:users,username,' . $userId;
            }
        }
        else
        {
            $rules['email'] .= '|unique:users,email';
            if (isset($data['username']))
            {
                $rules['username'] .= '|unique:users,username';
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails())
        {
            return [
                'valid' => false,
                'message' => @translate('validation_failed'),
                'errors' => $validator->errors()->toArray()
            ];
        }

        return ['valid' => true];
    }

    public function canDeleteUser(int $userId): array
    {
        try
        {
            $user = $this->userRepository->findUser($userId);

            if (!$user)
            {
                return [
                    'can_delete' => false,
                    'message' => @translate('user_not_found')
                ];
            }

            // Check if user is the current logged-in user
            if (auth()->id() === $userId)
            {
                return [
                    'can_delete' => false,
                    'message' => @translate('cannot_delete_yourself')
                ];
            }

            // Check if user has owner role
            if ($user->hasRole('owner'))
            {
                return [
                    'can_delete' => false,
                    'message' => @translate('cannot_delete_owner_user')
                ];
            }

            // Check if user is the only admin
            $adminCount = User::whereHas('roles', function ($query)
            {
                $query->where('name', 'admin');
            })->whereNotNull('customer_id')->count();

            if ($adminCount === 1 && $user->hasRole('admin'))
            {
                return [
                    'can_delete' => false,
                    'message' => @translate('cannot_delete_last_admin')
                ];
            }

            return [
                'can_delete' => true,
                'message' => @translate('user_can_be_deleted')
            ];
        }
        catch (\Exception $e)
        {
            return [
                'can_delete' => false,
                'message' => @translate('error_checking_user_deletion') . ': ' . $e->getMessage()
            ];
        }
    }

    public function sendPasswordResetEmail(int $userId): array
    {
        try
        {
            $user = $this->userRepository->findUser($userId);

            if (!$user)
            {
                return [
                    'success' => false,
                    'message' => @translate('user_not_found')
                ];
            }

            // Generate password reset token
            $token = $user->createResetToken();

            // Send email (implement your email logic here)
            // Mail::to($user->email)->send(new PasswordResetMail($user, $token));

            return [
                'success' => true,
                'message' => @translate('password_reset_email_sent_successfully'),
                'data' => ['email' => $user->email]
            ];
        }
        catch (\Exception $e)
        {
            return [
                'success' => false,
                'message' => @translate('error_sending_password_reset_email') . ': ' . $e->getMessage()
            ];
        }
    }
}
