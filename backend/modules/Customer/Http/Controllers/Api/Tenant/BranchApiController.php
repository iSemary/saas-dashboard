<?php

namespace Modules\Customer\Http\Controllers\Api\Tenant;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'is_active' => 'nullable|boolean',
        ]);
        return $this->apiSuccess($this->branchService->create($validated), 'Branch created successfully', 201);
    }

    public function show($id) { return $this->apiSuccess($this->branchService->findOrFail($id)); }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'is_active' => 'nullable|boolean',
        ]);
        $this->branchService->update($id, $validated);
        return $this->apiSuccess($this->branchService->findOrFail($id), 'Branch updated successfully');
    }

    public function destroy($id)
    {
        $this->branchService->delete($id);
        return $this->apiSuccess(null, 'Branch deleted successfully');
    }
}
