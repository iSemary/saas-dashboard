<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Development\DTOs\CreateIpBlacklistData;
use Modules\Development\DTOs\UpdateIpBlacklistData;
use Modules\Development\Http\Requests\StoreIpBlacklistRequest;
use Modules\Development\Http\Requests\UpdateIpBlacklistRequest;
use Modules\Development\Services\IpBlacklistService;

class IpBlacklistApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected IpBlacklistService $service) {}

    public function index(Request $request)
    {
        return $this->apiPaginated($this->service->list([], $request->get('per_page', 50)));
    }

    public function store(StoreIpBlacklistRequest $request)
    {
        $data = CreateIpBlacklistData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), translate('message.action_completed'), 201);
    }

    public function update(UpdateIpBlacklistRequest $request, $id)
    {
        $data = UpdateIpBlacklistData::fromRequest($request);
        return $this->apiSuccess($this->service->update($id, $data), translate('message.updated_successfully'));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, translate('message.action_completed'));
    }
}
