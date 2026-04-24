<?php

namespace Modules\Customer\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Customer\DTOs\CreateBrandData;
use Modules\Customer\DTOs\UpdateBrandData;
use Modules\Customer\Http\Requests\StoreBrandRequest;
use Modules\Customer\Http\Requests\UpdateBrandRequest;
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

    public function show($id) { return $this->apiSuccess($this->brandService->findOrFail($id)); }

    public function store(StoreBrandRequest $request)
    {
        $data = CreateBrandData::fromRequest($request);
        return $this->apiSuccess($this->brandService->create($data), 'Brand created successfully', 201);
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        $data = UpdateBrandData::fromRequest($request);
        $this->brandService->update($id, $data);
        return $this->apiSuccess($this->brandService->findOrFail($id), 'Brand updated successfully');
    }

    public function destroy($id)
    {
        $this->brandService->delete($id);
        return $this->apiSuccess(null, 'Brand deleted successfully');
    }
}
