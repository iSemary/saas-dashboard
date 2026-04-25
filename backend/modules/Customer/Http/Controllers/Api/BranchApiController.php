<?php

namespace Modules\Customer\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Customer\DTOs\CreateBranchData;
use Modules\Customer\Http\Requests\StoreBranchRequest;
use Modules\Customer\Http\Requests\UpdateBranchRequest;
use Modules\Customer\Services\BranchService;
use Modules\Tenant\Entities\Tenant;

class BranchApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected BranchService $branchService) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $originalTenantDatabase = config('database.connections.tenant.database');
        $rows = [];

        foreach (Tenant::on('landlord')->get(['id', 'name', 'database']) as $tenant) {
            if (empty($tenant->database)) {
                continue;
            }

            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            if (!Schema::connection('tenant')->hasTable('branches')) {
                continue;
            }

            $query = DB::connection('tenant')
                ->table('branches')
                ->leftJoin('brands', 'branches.brand_id', '=', 'brands.id')
                ->select([
                    'branches.id',
                    'branches.name',
                    'branches.code',
                    'branches.description',
                    'branches.email',
                    'branches.phone',
                    'branches.address',
                    'branches.status',
                    'branches.brand_id',
                    'branches.created_at',
                    'brands.name as brand_name',
                ]);

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('branches.name', 'like', '%' . $search . '%')
                        ->orWhere('branches.code', 'like', '%' . $search . '%')
                        ->orWhere('branches.description', 'like', '%' . $search . '%');
                });
            }

            foreach ($query->orderByDesc('branches.created_at')->get() as $branch) {
                $rows[] = [
                    'id' => ((int) $tenant->id * 1000000) + (int) $branch->id,
                    'tenant_record_id' => (int) $branch->id,
                    'tenant_id' => (int) $tenant->id,
                    'tenant' => [
                        'id' => (int) $tenant->id,
                        'name' => $tenant->name,
                    ],
                    'name' => $branch->name,
                    'slug' => $branch->code,
                    'description' => $branch->description,
                    'email' => $branch->email,
                    'phone' => $branch->phone,
                    'address' => $branch->address,
                    'status' => $branch->status,
                    'brand_id' => $branch->brand_id,
                    'brand' => $branch->brand_id ? ['id' => (int) $branch->brand_id, 'name' => $branch->brand_name] : null,
                    'created_at' => $branch->created_at,
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
        [$tenant, $tenantRecordId] = $this->resolveTenantRecord($id);
        if (!$tenant) {
            return $this->apiError('Branch not found', 404);
        }

        $originalTenantDatabase = config('database.connections.tenant.database');
        try {
            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            if (!Schema::connection('tenant')->hasTable('branches')) {
                return $this->apiError('Branch not found', 404);
            }

            $branch = DB::connection('tenant')
                ->table('branches')
                ->leftJoin('brands', 'branches.brand_id', '=', 'brands.id')
                ->select([
                    'branches.id',
                    'branches.name',
                    'branches.code',
                    'branches.description',
                    'branches.email',
                    'branches.phone',
                    'branches.address',
                    'branches.status',
                    'branches.brand_id',
                    'branches.created_at',
                    'brands.name as brand_name',
                ])
                ->where('branches.id', $tenantRecordId)
                ->first();

            if (!$branch) {
                return $this->apiError('Branch not found', 404);
            }

            return $this->apiSuccess($this->toLandlordBranchResponse($tenant, $branch));
        } finally {
            config(['database.connections.tenant.database' => $originalTenantDatabase]);
            DB::purge('tenant');
        }
    }

    public function store(StoreBranchRequest $request)
    {
        $data = CreateBranchData::fromRequest($request);
        return $this->apiSuccess($this->branchService->create($data), 'Branch created successfully', 201);
    }

    public function update(UpdateBranchRequest $request, $id)
    {
        [$tenant, $tenantRecordId] = $this->resolveTenantRecord($id);
        if (!$tenant) {
            return $this->apiError('Branch not found', 404);
        }

        $payload = array_filter([
            'name' => $request->input('name'),
            'code' => $request->input('code', $request->input('slug')),
            'description' => $request->input('description'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'brand_id' => $request->input('brand_id'),
            'status' => $request->input('status'),
        ], static fn ($value) => $value !== null);

        if ($request->has('is_active') && !$request->has('status')) {
            $payload['status'] = $request->boolean('is_active') ? 'active' : 'inactive';
        }

        $originalTenantDatabase = config('database.connections.tenant.database');
        try {
            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            if (!Schema::connection('tenant')->hasTable('branches')) {
                return $this->apiError('Branch not found', 404);
            }

            $columns = Schema::connection('tenant')->getColumnListing('branches');
            $payload = array_intersect_key($payload, array_flip($columns));

            if (!empty($payload)) {
                DB::connection('tenant')->table('branches')->where('id', $tenantRecordId)->update($payload);
            }

            $branch = DB::connection('tenant')
                ->table('branches')
                ->leftJoin('brands', 'branches.brand_id', '=', 'brands.id')
                ->select([
                    'branches.id',
                    'branches.name',
                    'branches.code',
                    'branches.description',
                    'branches.email',
                    'branches.phone',
                    'branches.address',
                    'branches.status',
                    'branches.brand_id',
                    'branches.created_at',
                    'brands.name as brand_name',
                ])
                ->where('branches.id', $tenantRecordId)
                ->first();

            if (!$branch) {
                return $this->apiError('Branch not found', 404);
            }

            return $this->apiSuccess($this->toLandlordBranchResponse($tenant, $branch), 'Branch updated successfully');
        } finally {
            config(['database.connections.tenant.database' => $originalTenantDatabase]);
            DB::purge('tenant');
        }
    }

    public function destroy($id)
    {
        [$tenant, $tenantRecordId] = $this->resolveTenantRecord($id);
        if (!$tenant) {
            return $this->apiError('Branch not found', 404);
        }

        $originalTenantDatabase = config('database.connections.tenant.database');
        try {
            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            if (!Schema::connection('tenant')->hasTable('branches')) {
                return $this->apiError('Branch not found', 404);
            }

            DB::connection('tenant')->table('branches')->where('id', $tenantRecordId)->delete();
            return $this->apiSuccess(null, 'Branch deleted successfully');
        } finally {
            config(['database.connections.tenant.database' => $originalTenantDatabase]);
            DB::purge('tenant');
        }
    }

    private function resolveTenantRecord($compositeId): array
    {
        $value = (int) $compositeId;
        $tenantId = (int) floor($value / 1000000);
        $recordId = $value % 1000000;

        $tenant = Tenant::on('landlord')->find($tenantId);
        if (!$tenant || empty($tenant->database)) {
            return [null, null];
        }

        return [$tenant, $recordId];
    }

    private function toLandlordBranchResponse(Tenant $tenant, object $branch): array
    {
        return [
            'id' => ((int) $tenant->id * 1000000) + (int) $branch->id,
            'tenant_record_id' => (int) $branch->id,
            'tenant_id' => (int) $tenant->id,
            'tenant' => [
                'id' => (int) $tenant->id,
                'name' => $tenant->name,
            ],
            'name' => $branch->name ?? null,
            'slug' => $branch->code ?? null,
            'description' => $branch->description ?? null,
            'email' => $branch->email ?? null,
            'phone' => $branch->phone ?? null,
            'address' => $branch->address ?? null,
            'status' => $branch->status ?? null,
            'brand_id' => $branch->brand_id ? (int) $branch->brand_id : null,
            'brand' => $branch->brand_id ? ['id' => (int) $branch->brand_id, 'name' => $branch->brand_name] : null,
            'created_at' => $branch->created_at ?? null,
        ];
    }
}
