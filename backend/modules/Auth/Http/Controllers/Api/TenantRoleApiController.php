<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\StoreRoleRequest;
use Modules\Auth\Http\Requests\UpdateRoleRequest;
use Modules\Auth\Services\Tenant\TenantRoleApiService;

class TenantRoleApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantRoleApiService $roleService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->roleService->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->roleService->findOrFail($id)); }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->create($request->validated());
        return $this->apiSuccess($role, 'Role created successfully', 201);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->roleService->update($id, $request->validated());
        return $this->apiSuccess($role, 'Role updated successfully');
    }

    public function destroy($id)
    {
        $this->roleService->delete($id);
        return $this->apiSuccess(null, 'Role deleted successfully');
    }
}
