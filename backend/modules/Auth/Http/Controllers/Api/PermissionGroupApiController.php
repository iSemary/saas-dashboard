<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Modules\Auth\Services\PermissionGroupService;
use Modules\Auth\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PermissionGroupApiController extends ApiController
{
    protected $service;
    protected $permissionService;

    public function __construct(PermissionGroupService $permissionGroupService, PermissionService $permissionService)
    {
        $this->service = $permissionGroupService;
        $this->permissionService = $permissionService;
    }

    /**
     * Get all permission groups
     */
    public function index(Request $request): JsonResponse
    {
        $permissionGroups = $this->service->getAll();
        
        return $this->return(200, 'Permission groups retrieved successfully', [
            'data' => $permissionGroups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'slug' => $group->slug,
                    'guard_name' => $group->guard_name,
                    'description' => $group->description,
                    'permissions_count' => $group->permissions_count ?? $group->permissions->count(),
                    'permissions' => $group->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Get a single permission group
     */
    public function show($id): JsonResponse
    {
        $permissionGroup = $this->service->get($id);
        
        if (!$permissionGroup) {
            return $this->return(404, 'Permission group not found');
        }

        return $this->return(200, 'Permission group retrieved successfully', [
            'data' => [
                'id' => $permissionGroup->id,
                'name' => $permissionGroup->name,
                'slug' => $permissionGroup->slug,
                'guard_name' => $permissionGroup->guard_name,
                'description' => $permissionGroup->description,
                'permissions_count' => $permissionGroup->permissions->count(),
                'permissions' => $permissionGroup->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Create a new permission group
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:125',
            'slug' => 'nullable|string|max:150|unique:permission_groups,slug',
            'guard_name' => 'nullable|string|max:125',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $data = $request->all();
        $data['guard_name'] = $data['guard_name'] ?? 'api';
        if (isset($data['permission_ids']) && ! isset($data['permissions'])) {
            $data['permissions'] = $data['permission_ids'];
        }
        unset($data['permission_ids']);

        $permissionGroup = $this->service->create($data);

        return $this->return(200, 'Permission group created successfully', [
            'data' => [
                'id' => $permissionGroup->id,
                'name' => $permissionGroup->name,
                'slug' => $permissionGroup->slug,
                'guard_name' => $permissionGroup->guard_name,
                'description' => $permissionGroup->description,
                'permissions_count' => $permissionGroup->permissions()->count(),
            ],
        ]);
    }

    /**
     * Update a permission group
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:125',
            'slug' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('permission_groups', 'slug')->ignore($id),
            ],
            'guard_name' => 'nullable|string|max:125',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $data = $request->all();
        if (isset($data['permission_ids']) && ! isset($data['permissions'])) {
            $data['permissions'] = $data['permission_ids'];
        }
        unset($data['permission_ids']);

        $permissionGroup = $this->service->update($id, $data);

        if (!$permissionGroup) {
            return $this->return(404, 'Permission group not found');
        }

        return $this->return(200, 'Permission group updated successfully', [
            'data' => [
                'id' => $permissionGroup->id,
                'name' => $permissionGroup->name,
                'slug' => $permissionGroup->slug,
                'guard_name' => $permissionGroup->guard_name,
                'description' => $permissionGroup->description,
                'permissions_count' => $permissionGroup->permissions()->count(),
            ],
        ]);
    }

    /**
     * Delete a permission group
     */
    public function destroy($id): JsonResponse
    {
        $result = $this->service->delete($id);

        if (!$result) {
            return $this->return(404, 'Permission group not found');
        }

        return $this->return(200, 'Permission group deleted successfully');
    }
}
