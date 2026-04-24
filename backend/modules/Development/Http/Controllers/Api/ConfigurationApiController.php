<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Development\DTOs\CreateConfigurationData;
use Modules\Development\DTOs\UpdateConfigurationData;
use Modules\Development\Http\Requests\StoreConfigurationRequest;
use Modules\Development\Http\Requests\UpdateConfigurationRequest;
use Modules\Development\Services\ConfigurationService;

class ConfigurationApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected ConfigurationService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(StoreConfigurationRequest $request)
    {
        $data = CreateConfigurationData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Configuration created successfully', 201);
    }

    public function update(UpdateConfigurationRequest $request, $id)
    {
        $data = UpdateConfigurationData::fromRequest($request);
        return $this->apiSuccess($this->service->update($id, $data), 'Configuration updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Configuration deleted successfully');
    }
}
