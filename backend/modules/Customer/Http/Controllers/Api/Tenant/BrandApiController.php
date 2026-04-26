<?php

namespace Modules\Customer\Http\Controllers\Api\Tenant;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use App\Services\CrossDb\LandlordService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Customer\DTOs\CreateBrandData;
use Modules\Customer\DTOs\UpdateBrandData;
use Modules\Customer\Services\BrandService;
use Modules\Customer\Services\BrandModuleSubscriptionService;

class BrandApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected BrandService $brandService,
        protected BrandModuleSubscriptionService $moduleService,
        protected LandlordService $landlordService,
    ) {}

    public function index(TableListRequest $request)
    {
        $params = $request->getTableParams();
        return $this->apiPaginated($this->brandService->getAll($params));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
        ]);
        $data = new CreateBrandData(
            name: $validated['name'],
            slug: $validated['slug'] ?? null,
            domain: $validated['domain'] ?? null,
            logo: null,
            is_active: $validated['is_active'] ?? true,
            modules: $validated['modules'] ?? null,
        );
        $brand = $this->brandService->create($data);

        // Assign modules if provided
        if (!empty($validated['modules'])) {
            $this->moduleService->subscribeToModules($brand->id, $validated['modules']);
        }

        return $this->apiSuccess($brand, translate('message.created_successfully'), 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
        ]);
        $data = new UpdateBrandData(...$validated);
        $this->brandService->update($id, $data);

        // Sync modules if provided
        if (isset($validated['modules'])) {
            $this->brandService->syncBrandModules($id, $validated['modules']);
        }

        return $this->apiSuccess($this->brandService->findOrFail($id), translate('message.updated_successfully'));
    }

    /**
     * Get available modules from landlord.
     */
    public function getAvailableModules(): \Illuminate\Http\JsonResponse
    {
        $modules = $this->landlordService->getModules(['status' => 'active']);
        return response()->json([
            'success' => true,
            'data' => $modules,
        ]);
    }

    public function destroy($id)
    {
        $this->brandService->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    /**
     * Get brand with its assigned modules.
     */
    public function show($id)
    {
        $brand = $this->brandService->findOrFail($id);
        $modules = $this->moduleService->getActiveSubscriptions($id);
        return $this->apiSuccess([
            ...$brand->toArray(),
            'modules' => $modules,
        ]);
    }
}
