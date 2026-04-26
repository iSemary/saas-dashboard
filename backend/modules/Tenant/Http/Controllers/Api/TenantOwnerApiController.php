<?php

namespace Modules\Tenant\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Services\TenantOwnerService;

class TenantOwnerApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantOwnerService $service) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $tenantIdFilter = $request->get('tenant_id');
        $statusFilter = $request->get('status');
        $originalTenantDatabase = config('database.connections.tenant.database');
        $rows = [];

        $tenantsQuery = Tenant::on('landlord')->get(['id', 'name', 'database']);
        if (!empty($tenantIdFilter)) {
            $tenantsQuery = $tenantsQuery->where('id', (int) $tenantIdFilter)->values();
        }

        foreach ($tenantsQuery as $tenant) {
            if (empty($tenant->database)) {
                continue;
            }

            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            if (!Schema::connection('tenant')->hasTable('users')) {
                continue;
            }

            $query = DB::connection('tenant')
                ->table('users')
                ->select(['id', 'name', 'email', 'created_at']);

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            }

            $effectiveStatus = $statusFilter ?: 'active';
            if ($statusFilter && $statusFilter !== 'active') {
                continue;
            }

            foreach ($query->orderByDesc('created_at')->get() as $user) {
                $rows[] = [
                    'id' => ((int) $tenant->id * 1000000) + (int) $user->id,
                    'tenant_id' => (int) $tenant->id,
                    'user_id' => (int) $user->id,
                    'role' => 'owner',
                    'is_super_admin' => false,
                    'status' => $effectiveStatus,
                    'permissions' => null,
                    'tenant' => [
                        'id' => (int) $tenant->id,
                        'name' => $tenant->name,
                    ],
                    'user' => [
                        'id' => (int) $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'created_at' => $user->created_at,
                ];
            }
        }

        config(['database.connections.tenant.database' => $originalTenantDatabase]);
        DB::purge('tenant');

        usort($rows, static fn (array $a, array $b) => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')));

        return $this->apiSuccess($rows);
    }

    public function show($id)
    {
        [$tenantId, $tenantOwnerId] = $this->decodeCompositeId($id);
        $tenant = Tenant::on('landlord')->find($tenantId);

        if (!$tenant || empty($tenant->database)) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }

        $originalTenantDatabase = config('database.connections.tenant.database');

        try {
            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            if (!Schema::connection('tenant')->hasTable('users')) {
                return $this->apiError(translate('message.resource_not_found'), 404);
            }

            $user = DB::connection('tenant')
                ->table('users')
                ->select(['id', 'name', 'email', 'created_at'])
                ->where('id', $tenantOwnerId)
                ->first();

            if (!$user) {
                return $this->apiError(translate('message.resource_not_found'), 404);
            }

            return $this->apiSuccess([
                'id' => ((int) $tenant->id * 1000000) + (int) $user->id,
                'tenant_id' => (int) $tenant->id,
                'user_id' => (int) $user->id,
                'role' => 'owner',
                'is_super_admin' => false,
                'status' => 'active',
                'permissions' => null,
                'tenant' => [
                    'id' => (int) $tenant->id,
                    'name' => $tenant->name,
                ],
                'user' => [
                    'id' => (int) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'created_at' => $user->created_at,
            ]);
        } finally {
            config(['database.connections.tenant.database' => $originalTenantDatabase]);
            DB::purge('tenant');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id'      => 'required|integer|exists:landlord.tenants,id',
            'user_id'        => 'required|integer|exists:landlord.users,id',
            'role'           => 'nullable|string|max:255',
            'is_super_admin' => 'nullable|boolean',
            'status'         => 'nullable|in:active,inactive,suspended',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'string',
        ]);

        $validated['role'] = $validated['role'] ?? 'owner';
        $validated['status'] = $validated['status'] ?? 'active';

        $tenantOwner = $this->service->create($validated);
        return $this->apiSuccess($tenantOwner->load(['tenant', 'user']), translate('message.created_successfully'), 201);
    }

    public function update(Request $request, $id)
    {
        $tenantOwner = $this->service->getById($id);
        if (!$tenantOwner) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }

        $validated = $request->validate([
            'tenant_id'      => 'sometimes|integer|exists:landlord.tenants,id',
            'user_id'        => 'sometimes|integer|exists:landlord.users,id',
            'role'           => 'sometimes|nullable|string|max:255',
            'is_super_admin' => 'sometimes|nullable|boolean',
            'status'         => 'sometimes|nullable|in:active,inactive,suspended',
            'permissions'    => 'sometimes|nullable|array',
            'permissions.*'  => 'string',
        ]);

        $this->service->update($id, $validated);
        return $this->apiSuccess($this->service->getById($id)->load(['tenant', 'user']), translate('message.updated_successfully'));
    }

    public function destroy($id)
    {
        $tenantOwner = $this->service->getById($id);
        if (!$tenantOwner) {
            return $this->apiError(translate('message.resource_not_found'), 404);
        }
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    private function decodeCompositeId($id): array
    {
        $value = (int) $id;

        return [
            (int) floor($value / 1000000),
            $value % 1000000,
        ];
    }
}
