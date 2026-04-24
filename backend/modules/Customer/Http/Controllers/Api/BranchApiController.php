<?php

namespace Modules\Customer\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Customer\DTOs\CreateBranchData;
use Modules\Customer\DTOs\UpdateBranchData;
use Modules\Customer\Http\Requests\StoreBranchRequest;
use Modules\Customer\Http\Requests\UpdateBranchRequest;
use Modules\Customer\Services\BranchService;

class BranchApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected BranchService $branchService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->branchService->getAll($filters, $request->get('per_page', 50)));
    }

    public function show($id) { return $this->apiSuccess($this->branchService->findOrFail($id)); }

    public function store(StoreBranchRequest $request)
    {
        $data = CreateBranchData::fromRequest($request);
        return $this->apiSuccess($this->branchService->create($data), 'Branch created successfully', 201);
    }

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
