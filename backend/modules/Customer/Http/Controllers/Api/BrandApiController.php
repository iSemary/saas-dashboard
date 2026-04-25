<?php

namespace Modules\Customer\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Customer\DTOs\CreateBrandData;
use Modules\Customer\Http\Requests\StoreBrandRequest;
use Modules\Customer\Http\Requests\UpdateBrandRequest;
use Modules\Customer\Services\BrandService;
use Modules\Tenant\Entities\Tenant;

class BrandApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected BrandService $brandService) {}

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

            if (!Schema::connection('tenant')->hasTable('brands')) {
                continue;
            }

            $query = DB::connection('tenant')
                ->table('brands')
                ->select([
                    'id',
                    'name',
                    'slug',
                    'description',
                    'logo',
                    'website',
                    'email',
                    'phone',
                    'address',
                    'status',
                    'created_at',
                ]);

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            foreach ($query->orderByDesc('created_at')->get() as $brand) {
                $rows[] = [
                    'id' => ((int) $tenant->id * 1000000) + (int) $brand->id,
                    'tenant_record_id' => (int) $brand->id,
                    'tenant_id' => (int) $tenant->id,
                    'tenant' => [
                        'id' => (int) $tenant->id,
                        'name' => $tenant->name,
                    ],
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'description' => $brand->description,
                    'logo' => $brand->logo,
                    'website' => $brand->website,
                    'email' => $brand->email,
                    'phone' => $brand->phone,
                    'address' => $brand->address,
                    'status' => $brand->status,
                    'created_at' => $brand->created_at,
                ];
            }
        }

        config(['database.connections.tenant.database' => $originalTenantDatabase]);
        DB::purge('tenant');

        usort($rows, static fn (array $a, array $b) => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')));

        return $this->apiSuccess($rows);
    }

    public function store(StoreBrandRequest $request)
    {
        $data = CreateBrandData::fromRequest($request);
        return $this->apiSuccess($this->brandService->create($data), 'Brand created successfully', 201);
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        [$tenant, $tenantRecordId] = $this->resolveTenantRecord($id);
        if (!$tenant) {
            return $this->apiError('Brand not found', 404);
        }

        $payload = array_filter([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'website' => $request->input('website'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'status' => $request->input('status'),
        ], static fn ($value) => $value !== null);

        if ($request->has('is_active') && !$request->has('status')) {
            $payload['status'] = $request->boolean('is_active') ? 'active' : 'inactive';
        }

        $originalTenantDatabase = config('database.connections.tenant.database');

        try {
            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            if (!Schema::connection('tenant')->hasTable('brands')) {
                return $this->apiError('Brand not found', 404);
            }

            $columns = Schema::connection('tenant')->getColumnListing('brands');
            $payload = array_intersect_key($payload, array_flip($columns));

            if (!empty($payload)) {
                DB::connection('tenant')->table('brands')->where('id', $tenantRecordId)->update($payload);
            }

            $brand = DB::connection('tenant')->table('brands')->where('id', $tenantRecordId)->first();
            if (!$brand) {
                return $this->apiError('Brand not found', 404);
            }

            return $this->apiSuccess($this->toLandlordBrandResponse($tenant, $brand), 'Brand updated successfully');
        } finally {
            config(['database.connections.tenant.database' => $originalTenantDatabase]);
            DB::purge('tenant');
        }
    }

    public function destroy($id)
    {
        [$tenant, $tenantRecordId] = $this->resolveTenantRecord($id);
        if (!$tenant) {
            return $this->apiError('Brand not found', 404);
        }

        $originalTenantDatabase = config('database.connections.tenant.database');
        try {
            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            if (!Schema::connection('tenant')->hasTable('brands')) {
                return $this->apiError('Brand not found', 404);
            }

            DB::connection('tenant')->table('brands')->where('id', $tenantRecordId)->delete();
            return $this->apiSuccess(null, 'Brand deleted successfully');
        } finally {
            config(['database.connections.tenant.database' => $originalTenantDatabase]);
            DB::purge('tenant');
        }
    }

    public function show($id)
    {
        [$tenant, $tenantRecordId] = $this->resolveTenantRecord($id);
        if (!$tenant) {
            return $this->apiError('Brand not found', 404);
        }

        $originalTenantDatabase = config('database.connections.tenant.database');
        try {
            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');

            if (!Schema::connection('tenant')->hasTable('brands')) {
                return $this->apiError('Brand not found', 404);
            }

            $brand = DB::connection('tenant')->table('brands')->where('id', $tenantRecordId)->first();
            if (!$brand) {
                return $this->apiError('Brand not found', 404);
            }

            return $this->apiSuccess($this->toLandlordBrandResponse($tenant, $brand));
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

    private function toLandlordBrandResponse(Tenant $tenant, object $brand): array
    {
        return [
            'id' => ((int) $tenant->id * 1000000) + (int) $brand->id,
            'tenant_record_id' => (int) $brand->id,
            'tenant_id' => (int) $tenant->id,
            'tenant' => [
                'id' => (int) $tenant->id,
                'name' => $tenant->name,
            ],
            'name' => $brand->name ?? null,
            'slug' => $brand->slug ?? null,
            'description' => $brand->description ?? null,
            'logo' => $brand->logo ?? null,
            'website' => $brand->website ?? null,
            'email' => $brand->email ?? null,
            'phone' => $brand->phone ?? null,
            'address' => $brand->address ?? null,
            'status' => $brand->status ?? null,
            'created_at' => $brand->created_at ?? null,
        ];
    }
}
