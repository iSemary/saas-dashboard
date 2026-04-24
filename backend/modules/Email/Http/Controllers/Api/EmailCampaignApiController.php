<?php

namespace Modules\Email\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Email\DTOs\CreateEmailCampaignData;
use Modules\Email\Http\Requests\StoreEmailCampaignRequest;
use Modules\Email\Services\EmailCampaignService;

class EmailCampaignApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected EmailCampaignService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function store(StoreEmailCampaignRequest $request)
    {
        $data = CreateEmailCampaignData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Campaign created successfully', 201);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Campaign deleted successfully');
    }
}
