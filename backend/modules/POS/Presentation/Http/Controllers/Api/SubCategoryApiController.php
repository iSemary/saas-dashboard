<?php

namespace Modules\POS\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\POS\Application\Services\SubCategoryService;
use Modules\POS\Presentation\Http\Requests\StoreSubCategoryRequest;

class SubCategoryApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly SubCategoryService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'category_id']);

            if ($request->boolean('all')) {
                return $this->apiSuccess($this->service->all($filters));
            }

            return $this->apiPaginated($this->service->list($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve sub-categories', 500, $e->getMessage());
        }
    }

    public function store(StoreSubCategoryRequest $request): JsonResponse
    {
        try {
            $sub = $this->service->create($request->validated(), auth()->id());
            return $this->apiSuccess($sub, 'Sub-category created successfully', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create sub-category', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('Sub-category not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'name'        => 'required|string|max:255',
                'category_id' => 'nullable|integer|exists:pos_categories,id',
                'branch_id'   => 'nullable|integer',
            ]);
            $sub = $this->service->update($id, $request->only(['name', 'category_id', 'branch_id']));
            return $this->apiSuccess($sub, 'Sub-category updated successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update sub-category', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return $this->apiSuccess(null, 'Sub-category deleted successfully');
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete sub-category', 500, $e->getMessage());
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate(['ids' => 'required|array']);
            $count = $this->service->bulkDelete($request->ids);
            return $this->apiSuccess(null, "{$count} sub-categories deleted successfully");
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete sub-categories', 500, $e->getMessage());
        }
    }
}
