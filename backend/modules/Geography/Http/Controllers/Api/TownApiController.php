<?php

namespace Modules\Geography\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Geography\DTOs\CreateTownData;
use Modules\Geography\Http\Requests\StoreTownRequest;
use Modules\Geography\Services\TownService;

class TownApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TownService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'city_id']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(StoreTownRequest $request)
    {
        $data = CreateTownData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Town created successfully', 201);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Town deleted successfully');
    }
}
