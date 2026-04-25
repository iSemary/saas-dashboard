<?php

namespace Modules\Utilities\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Utilities\DTOs\CreateIndustryData;
use Modules\Utilities\Http\Requests\StoreIndustryRequest;
use Modules\Utilities\Services\IndustryService;

class IndustryApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected IndustryService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(StoreIndustryRequest $request)
    {
        $data = CreateIndustryData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Industry created successfully', 201);
    }

    public function update(StoreIndustryRequest $request, $id)
    {
        $data = CreateIndustryData::fromRequest($request);
        return $this->apiSuccess($this->service->update($id, (array) $data), 'Industry updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Industry deleted successfully');
    }
}
