<?php

namespace Modules\Tenant\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Tenant\DTOs\CreateTenantData;
use Modules\Tenant\DTOs\UpdateTenantData;
use Modules\Tenant\Http\Requests\StoreTenantRequest;
use Modules\Tenant\Http\Requests\UpdateTenantRequest;
use Modules\Tenant\Services\TenantService;

class TenantApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TenantService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id)
    {
        return $this->apiSuccess($this->service->findOrFail($id));
    }

    public function store(StoreTenantRequest $request)
    {
        $data = CreateTenantData::fromRequest($request);
        $tenant = $this->service->create($data);
        return $this->apiSuccess($tenant, 'Tenant created successfully', 201);
    }

    public function update(UpdateTenantRequest $request, $id)
    {
        $data = UpdateTenantData::fromRequest($request);
        $this->service->update($id, $data);
        return $this->apiSuccess($this->service->findOrFail($id), 'Tenant updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Tenant deleted successfully');
    }
}
