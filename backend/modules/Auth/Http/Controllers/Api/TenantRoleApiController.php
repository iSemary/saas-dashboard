<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\StoreRoleRequest;
use Modules\Auth\Http\Requests\UpdateRoleRequest;
use Modules\Auth\Services\Tenant\TenantRoleApiService;

class TenantRoleApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantRoleApiService $roleService) {}

    public function index(TableListRequest $request)
    {
        $params = $request->getTableParams();
        return $this->apiPaginated($this->roleService->list($params));
    }

    public function show($id) { return $this->apiSuccess($this->roleService->findOrFail($id)); }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->create($request->validated());
        return $this->apiSuccess($role, translate('message.created_successfully'), 201);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->roleService->update($id, $request->validated());
        return $this->apiSuccess($role, translate('message.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->roleService->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
