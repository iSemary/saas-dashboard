<?php

namespace Modules\Geography\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Geography\DTOs\CreateCityData;
use Modules\Geography\Http\Requests\StoreCityRequest;
use Modules\Geography\Services\CityService;

class CityApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected CityService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'province_id']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(StoreCityRequest $request)
    {
        $data = CreateCityData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'City created successfully', 201);
    }

    public function update(StoreCityRequest $request, $id)
    {
        $data = CreateCityData::fromRequest($request);
        $city = $this->service->update($id, [
            'name' => $data->name,
            'province_id' => $data->province_id,
        ]);
        return $this->apiSuccess($city, 'City updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'City deleted successfully');
    }
}
