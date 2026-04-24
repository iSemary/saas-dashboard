<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Development\DTOs\CreateFeatureFlagData;
use Modules\Development\DTOs\UpdateFeatureFlagData;
use Modules\Development\Http\Requests\StoreFeatureFlagRequest;
use Modules\Development\Http\Requests\UpdateFeatureFlagRequest;
use Modules\Development\Services\FeatureFlagService;

class FeatureFlagApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected FeatureFlagService $service) {}

    public function index(Request $request)
    {
        return $this->apiPaginated($this->service->list($request->get('per_page', 50)));
    }

    public function store(StoreFeatureFlagRequest $request)
    {
        $data = CreateFeatureFlagData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Feature flag created successfully', 201);
    }

    public function update(UpdateFeatureFlagRequest $request, $id)
    {
        $data = UpdateFeatureFlagData::fromRequest($request);
        return $this->apiSuccess($this->service->update($id, $data), 'Feature flag updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Feature flag deleted successfully');
    }
}
