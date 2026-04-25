<?php

namespace Modules\Geography\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Geography\DTOs\CreateStreetData;
use Modules\Geography\Http\Requests\StoreStreetRequest;
use Modules\Geography\Http\Requests\UpdateStreetRequest;
use Modules\Geography\Services\StreetService;

class StreetApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected StreetService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'town_id']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(StoreStreetRequest $request)
    {
        $data = CreateStreetData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Street created successfully', 201);
    }

    public function update(UpdateStreetRequest $request, $id)
    {
        return $this->apiSuccess($this->service->update($id, $request->validated()), 'Street updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Street deleted successfully');
    }
}
