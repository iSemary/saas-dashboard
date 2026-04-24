<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
        $user = $this->userService->create($validated);
        return $this->apiSuccess($user, 'User created successfully', 201);
    }

    public function show($id) { return $this->apiSuccess($this->userService->findOrFail($id)); }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => "sometimes|required|email|max:255|unique:users,email,{$id}",
            'password' => 'nullable|string|min:8',
        ]);
        $user = $this->userService->update($id, $validated);
        return $this->apiSuccess($user, 'User updated successfully');
    }

    public function destroy($id)
    {
        $this->userService->delete($id);
        return $this->apiSuccess(null, 'User deleted successfully');
    }

    public function assignRoles(Request $request, $id)
    {
        $validated = $request->validate(['role_ids' => 'required|array']);
        $user = $this->userService->assignRoles($id, $validated['role_ids']);
        return $this->apiSuccess($user, 'Roles assigned successfully');
    }
}
