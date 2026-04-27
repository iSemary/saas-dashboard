<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Entities\Role;
use Modules\Auth\Entities\Permission;
use Modules\Auth\Repositories\Tenant\TenantRoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantRoleService implements TenantRoleServiceInterface
{
    protected $roleRepository;

    public function __construct(TenantRoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAllRoles(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        try {
            return $this->roleRepository->getRolesWithPagination($filters, $perPage);
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_fetching_roles') . ': ' . $e->getMessage());
        }
    }

    public function getRoleById(int $id): ?Role
    {
        try {
            return $this->roleRepository->findRole($id);
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_fetching_role') . ': ' . $e->getMessage());
        }
    }

    public function createRole(array $data): array
    {
        // Validate data
        $validation = $this->validateRoleData($data);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'] ?? []
            ];
        }

        try {
            $role = $this->roleRepository->createRole($data);
            
            return [
                'success' => true,
                'message' => @translate('role_created_successfully'),
                'data' => $role
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_creating_role') . ': ' . $e->getMessage()
            ];
        }
    }

    public function updateRole(int $id, array $data): array
    {
        // Validate data
        $validation = $this->validateRoleData($data, $id);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'] ?? []
            ];
        }

        try {
            $role = $this->roleRepository->updateRole($id, $data);
            
            return [
                'success' => true,
                'message' => @translate('role_updated_successfully'),
                'data' => $role
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_updating_role') . ': ' . $e->getMessage()
            ];
        }
    }

    public function deleteRole(int $id): array
    {
        // Check if role can be deleted
        $canDelete = $this->canDeleteRole($id);
        if (!$canDelete['can_delete']) {
            return [
                'success' => false,
                'message' => $canDelete['message']
            ];
        }

        try {
            $result = $this->roleRepository->deleteRole($id);
            
            return [
                'success' => true,
                'message' => @translate('role_deleted_successfully'),
                'data' => ['deleted' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_deleting_role') . ': ' . $e->getMessage()
            ];
        }
    }

    public function softDeleteRole(int $id): array
    {
        try {
            $result = $this->roleRepository->softDeleteRole($id);
            
            return [
                'success' => true,
                'message' => @translate('role_soft_deleted_successfully'),
                'data' => ['deleted' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_deleting_role') . ': ' . $e->getMessage()
            ];
        }
    }

    public function restoreRole(int $id): array
    {
        try {
            $result = $this->roleRepository->restoreRole($id);
            
            return [
                'success' => true,
                'message' => @translate('role_restored_successfully'),
                'data' => ['restored' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_restoring_role') . ': ' . $e->getMessage()
            ];
        }
    }

    public function assignPermissionsToRole(int $roleId, array $permissionIds): array
    {
        try {
            $result = $this->roleRepository->assignPermissionsToRole($roleId, $permissionIds);
            
            return [
                'success' => true,
                'message' => @translate('permissions_assigned_to_role_successfully'),
                'data' => ['assigned' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_assigning_permissions') . ': ' . $e->getMessage()
            ];
        }
    }

    public function removePermissionsFromRole(int $roleId, array $permissionIds): array
    {
        try {
            $result = $this->roleRepository->removePermissionsFromRole($roleId, $permissionIds);
            
            return [
                'success' => true,
                'message' => @translate('permissions_removed_from_role_successfully'),
                'data' => ['removed' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_removing_permissions') . ': ' . $e->getMessage()
            ];
        }
    }

    public function syncPermissionsForRole(int $roleId, array $permissionIds): array
    {
        try {
            $result = $this->roleRepository->syncPermissionsForRole($roleId, $permissionIds);
            
            return [
                'success' => true,
                'message' => @translate('role_permissions_synced_successfully'),
                'data' => ['synced' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_syncing_permissions') . ': ' . $e->getMessage()
            ];
        }
    }

    public function getPermissionsForRole(int $roleId): Collection
    {
        try {
            $role = $this->roleRepository->findRole($roleId);
            return $role ? $role->permissions : collect([]);
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_fetching_role_permissions') . ': ' . $e->getMessage());
        }
    }

    public function getAvailablePermissions(): Collection
    {
        try {
            return Permission::where('guard_name', 'web')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_fetching_permissions') . ': ' . $e->getMessage());
        }
    }

    public function getRoleStatistics(): array
    {
        try {
            return $this->roleRepository->getRoleStatistics();
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_fetching_role_statistics') . ': ' . $e->getMessage());
        }
    }

    public function validateRoleData(array $data, int $roleId = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'guard_name' => 'string|in:web,api',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id'
        ];

        // Update uniqueness rule for editing
        if ($roleId) {
            $rules['name'] .= '|unique:roles,name,' . $roleId;
        } else {
            $rules['name'] .= '|unique:roles,name';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'message' => @translate('validation_failed'),
                'errors' => $validator->errors()->toArray()
            ];
        }

        return ['valid' => true];
    }

    public function canDeleteRole(int $roleId): array
    {
        try {
            $role = $this->roleRepository->findRole($roleId);
            
            if (!$role) {
                return [
                    'can_delete' => false,
                    'message' => @translate('role_not_found')
                ];
            }

            // Check if role has users
            if ($role->users()->count() > 0) {
                return [
                    'can_delete' => false,
                    'message' => @translate('cannot_delete_role_with_assigned_users')
                ];
            }

            // Check if it's a protected role
            $protectedRoles = ['owner', 'admin', 'super_admin'];
            if (in_array($role->name, $protectedRoles)) {
                return [
                    'can_delete' => false,
                    'message' => @translate('cannot_delete_protected_role')
                ];
            }

            return [
                'can_delete' => true,
                'message' => @translate('role_can_be_deleted')
            ];
        } catch (\Exception $e) {
            return [
                'can_delete' => false,
                'message' => @translate('error_checking_role_deletion') . ': ' . $e->getMessage()
            ];
        }
    }
}
