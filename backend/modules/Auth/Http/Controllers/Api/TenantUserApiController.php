<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Http\Requests\AssignRolesRequest;
use Modules\Auth\Http\Requests\StoreUserRequest;
use Modules\Auth\Http\Requests\UpdateUserRequest;
use Modules\Auth\Services\Tenant\TenantUserApiService;

class TenantUserApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantUserApiService $userService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->userService->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->userService->findOrFail($id)); }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create($request->validated());
        return $this->apiSuccess($user, 'User created successfully', 201);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->update($id, $request->validated());
        return $this->apiSuccess($user, 'User updated successfully');
    }

    public function destroy($id)
    {
        $this->userService->delete($id);
        return $this->apiSuccess(null, 'User deleted successfully');
    }

    public function assignRoles(AssignRolesRequest $request, $id)
    {
        $user = $this->userService->assignRoles($id, $request->role_ids);
        return $this->apiSuccess($user, 'Roles assigned successfully');
    }
}
