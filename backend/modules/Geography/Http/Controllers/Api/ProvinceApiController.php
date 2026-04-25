<?php

namespace Modules\Geography\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Geography\DTOs\CreateProvinceData;
use Modules\Geography\Http\Requests\StoreProvinceRequest;
use Modules\Geography\Http\Requests\UpdateProvinceRequest;
use Modules\Geography\Services\ProvinceService;

class ProvinceApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected ProvinceService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'country_id']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(StoreProvinceRequest $request)
    {
        $data = CreateProvinceData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Province created successfully', 201);
    }

    public function update(UpdateProvinceRequest $request, $id)
    {
        return $this->apiSuccess($this->service->update($id, $request->validated()), 'Province updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Province deleted successfully');
    }
}
