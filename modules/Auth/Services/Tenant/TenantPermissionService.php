<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Entities\Permission;
use Modules\Auth\Entities\Role;
use Modules\Auth\Repositories\Tenant\TenantPermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantPermissionService implements TenantPermissionServiceInterface
{
    protected $permissionRepository;

    public function __construct(TenantPermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function getDataTables()
    {
        return $this->permissionRepository->datatables();
    }

    public function getAllPermissions(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        try {
            return $this->permissionRepository->getPermissionsWithPagination($filters, $perPage);
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_fetching_permissions') . ': ' . $e->getMessage());
        }
    }

    public function getPermissionById(int $id): ?Permission
    {
        try {
            return $this->permissionRepository->findPermission($id);
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_fetching_permission') . ': ' . $e->getMessage());
        }
    }

    public function createPermission(array $data): array
    {
        // Validate data
        $validation = $this->validatePermissionData($data);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'] ?? []
            ];
        }

        try {
            $permission = $this->permissionRepository->createPermission($data);
            
            return [
                'success' => true,
                'message' => @translate('permission_created_successfully'),
                'data' => $permission
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_creating_permission') . ': ' . $e->getMessage()
            ];
        }
    }

    public function updatePermission(int $id, array $data): array
    {
        // Validate data
        $validation = $this->validatePermissionData($data, $id);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'] ?? []
            ];
        }

        try {
            $permission = $this->permissionRepository->updatePermission($id, $data);
            
            return [
                'success' => true,
                'message' => @translate('permission_updated_successfully'),
                'data' => $permission
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_updating_permission') . ': ' . $e->getMessage()
            ];
        }
    }

    public function deletePermission(int $id): array
    {
        // Check if permission can be deleted
        $canDelete = $this->canDeletePermission($id);
        if (!$canDelete['can_delete']) {
            return [
                'success' => false,
                'message' => $canDelete['message']
            ];
        }

        try {
            $result = $this->permissionRepository->deletePermission($id);
            
            return [
                'success' => true,
                'message' => @translate('permission_deleted_successfully'),
                'data' => ['deleted' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_deleting_permission') . ': ' . $e->getMessage()
            ];
        }
    }

    public function softDeletePermission(int $id): array
    {
        try {
            $result = $this->permissionRepository->softDeletePermission($id);
            
            return [
                'success' => true,
                'message' => @translate('permission_soft_deleted_successfully'),
                'data' => ['deleted' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_deleting_permission') . ': ' . $e->getMessage()
            ];
        }
    }

    public function restorePermission(int $id): array
    {
        try {
            $result = $this->permissionRepository->restorePermission($id);
            
            return [
                'success' => true,
                'message' => @translate('permission_restored_successfully'),
                'data' => ['restored' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_restoring_permission') . ': ' . $e->getMessage()
            ];
        }
    }

    public function assignPermissionToRoles(int $permissionId, array $roleIds): array
    {
        try {
            $result = $this->permissionRepository->assignPermissionToRoles($permissionId, $roleIds);
            
            return [
                'success' => true,
                'message' => @translate('permission_assigned_to_roles_successfully'),
                'data' => ['assigned' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_assigning_permission') . ': ' . $e->getMessage()
            ];
        }
    }

    public function removePermissionFromRoles(int $permissionId, array $roleIds): array
    {
        try {
            $result = $this->permissionRepository->removePermissionFromRoles($permissionId, $roleIds);
            
            return [
                'success' => true,
                'message' => @translate('permission_removed_from_roles_successfully'),
                'data' => ['removed' => $result]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_removing_permission') . ': ' . $e->getMessage()
            ];
        }
    }

    public function bulkCreateResourcePermissions(string $resource, array $actions = ['view', 'create', 'update', 'delete']): array
    {
        try {
            $permissions = $this->permissionRepository->bulkCreateResourcePermissions($resource, $actions);
            
            return [
                'success' => true,
                'message' => @translate('resource_permissions_created_successfully'),
                'data' => $permissions
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => @translate('error_creating_resource_permissions') . ': ' . $e->getMessage()
            ];
        }
    }

    public function getPermissionsGroupedByResource(): Collection
    {
        try {
            return $this->permissionRepository->getPermissionsGroupedByResource();
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_grouping_permissions') . ': ' . $e->getMessage());
        }
    }

    public function getPermissionsByAction(string $action): Collection
    {
        try {
            return $this->permissionRepository->getPermissionsByAction($action);
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_fetching_permissions_by_action') . ': ' . $e->getMessage());
        }
    }

    public function getPermissionStatistics(): array
    {
        try {
            return $this->permissionRepository->getPermissionStatistics();
        } catch (\Exception $e) {
            throw new \Exception(@translate('error_fetching_permission_statistics') . ': ' . $e->getMessage());
        }
    }

    public function validatePermissionData(array $data, int $permissionId = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'guard_name' => 'string|in:web,api',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id'
        ];

        // Update uniqueness rule for editing
        if ($permissionId) {
            $rules['name'] .= '|unique:permissions,name,' . $permissionId;
        } else {
            $rules['name'] .= '|unique:permissions,name';
        }

        // Validate permission name format (action.resource)
        if (isset($data['name'])) {
            if (!preg_match('/^[a-z_]+(\.[a-z_]+)+$/', $data['name'])) {
                return [
                    'valid' => false,
                    'message' => @translate('permission_name_must_be_in_format_action_resource')
                ];
            }
        }

        $validator = Validator::make($data, $rules);

        return $validator->fails() ? [
            'valid' => false,
            'message' => @translate('validation_failed'),
            'errors' => $validator->errors()->toArray()
        ] : ['valid' => true];
    }

    public function canDeletePermission(int $permissionId): array
    {
        try {
            $permission = $this->permissionRepository->findPermission($permissionId);
            
            if (!$permission) {
                return [
                    'can_delete' => false,
                    'message' => @translate('permission_not_found')
                ];
            }

            // Check if permission is assigned to roles
            if ($permission->roles()->count() > 0) {
                return [
                    'can_delete' => false,
                    'message' => @translate('cannot_delete_permission_assigned_to_roles')
                ];
            }

            // Check if permission is directly assigned to users
            $directUserCount = DB::table('model_has_permissions')
                ->where('permission_id', $permissionId)
                ->count();

            if ($directUserCount > 0) {
                return [
                    'can_delete' => false,
                    'message' => @translate('cannot_delete_permission_assigned_to_users')
                ];
            }

            // Check if it's a system permission
            $systemPermissions = [
                'view.dashboard',
                'create.users',
                'update.users',
                'delete.users'
            ];

            if (in_array($permission->name, $systemPermissions)) {
                return [
                    'can_delete' => false,
                    'message' => @translate('cannot_delete_system_permission')
                ];
            }

            return [
                'can_delete' => true,
                'message' => @translate('permission_can_be_deleted')
            ];
        } catch (\Exception $e) {
            return [
                'can_delete' => false,
                'message' => @translate('error_checking_permission_deletion') . ': ' . $e->getMessage()
            ];
        }
    }
}
