<?php

namespace Modules\Customer\Http\Controllers\Api\Tenant;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Customer\Services\BrandService;

class BrandApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected BrandService $brandService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        return $this->apiPaginated($this->brandService->getAll($filters, $request->get('per_page', 50)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        return $this->apiSuccess($this->brandService->create($validated), 'Brand created successfully', 201);
    }

    public function show($id) { return $this->apiSuccess($this->brandService->findOrFail($id)); }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $this->brandService->update($id, $validated);
        return $this->apiSuccess($this->brandService->findOrFail($id), 'Brand updated successfully');
    }

    public function destroy($id)
    {
        $this->brandService->delete($id);
        return $this->apiSuccess(null, 'Brand deleted successfully');
    }
}
