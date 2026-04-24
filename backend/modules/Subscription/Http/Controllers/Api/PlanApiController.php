<?php

namespace Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Subscription\DTOs\CreatePlanData;
use Modules\Subscription\DTOs\UpdatePlanData;
use Modules\Subscription\Http\Requests\StorePlanRequest;
use Modules\Subscription\Http\Requests\UpdatePlanRequest;
use Modules\Subscription\Services\PlanService;

class PlanApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected PlanService $service) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->service->list($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->service->findOrFail($id)); }

    public function store(StorePlanRequest $request)
    {
        $data = CreatePlanData::fromRequest($request);
        return $this->apiSuccess($this->service->create($data), 'Plan created successfully', 201);
    }

    public function update(UpdatePlanRequest $request, $id)
    {
        $data = UpdatePlanData::fromRequest($request);
        $this->service->update($id, $data);
        return $this->apiSuccess($this->service->findOrFail($id), 'Plan updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->apiSuccess(null, 'Plan deleted successfully');
    }
}
