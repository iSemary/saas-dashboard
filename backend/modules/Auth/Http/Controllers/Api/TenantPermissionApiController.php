<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\StorePermissionRequest;
use Modules\Auth\Http\Requests\UpdatePermissionRequest;
use Modules\Auth\Services\Tenant\TenantPermissionApiService;

class TenantPermissionApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantPermissionApiService $permissionService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->permissionService->list($filters, $request->get('per_page', 50)));
    }

    public function store(StorePermissionRequest $request)
    {
        return $this->apiSuccess($this->permissionService->create($request->validated()), 'Permission created successfully', 201);
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        $permission = $this->permissionService->update($id, $request->validated());
        return $this->apiSuccess($permission, 'Permission updated successfully');
    }

    public function destroy($id)
    {
        $this->permissionService->delete($id);
        return $this->apiSuccess(null, 'Permission deleted successfully');
    }
}
