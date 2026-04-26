<?php

namespace Modules\Customer\Http\Controllers\Api\Tenant;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Modules\Customer\Http\Requests\StoreBranchRequest;
use Modules\Customer\Http\Requests\UpdateBranchRequest;
use Modules\Customer\DTOs\CreateBranchData;
use Modules\Customer\DTOs\UpdateBranchData;
use Modules\Customer\Services\BranchService;
use Illuminate\Routing\Controller;

class BranchApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected BranchService $branchService) {}

    public function index(TableListRequest $request)
    {
        $params = $request->getTableParams();
        return $this->apiPaginated($this->branchService->getAll($params));
    }

    public function store(StoreBranchRequest $request)
    {
        $data = CreateBranchData::fromRequest($request);
        return $this->apiSuccess($this->branchService->create($data), 'Branch created successfully', 201);
    }

    public function show($id) { return $this->apiSuccess($this->branchService->findOrFail($id)); }

    public function update(UpdateBranchRequest $request, $id)
    {
        $data = UpdateBranchData::fromRequest($request);
        $this->branchService->update($id, $data);
        return $this->apiSuccess($this->branchService->findOrFail($id), 'Branch updated successfully');
    }

    public function destroy($id)
    {
        $this->branchService->delete($id);
        return $this->apiSuccess(null, 'Branch deleted successfully');
    }
}
