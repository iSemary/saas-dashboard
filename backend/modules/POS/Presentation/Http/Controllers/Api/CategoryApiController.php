<?php

namespace Modules\POS\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\POS\Application\Services\CategoryService;
use Modules\POS\Presentation\Http\Requests\StoreCategoryRequest;

class CategoryApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly CategoryService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'branch_id']);

            if ($request->boolean('all')) {
                return $this->apiSuccess($this->service->all($filters));
            }

            return $this->apiPaginated($this->service->list($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve categories', 500, $e->getMessage());
        }
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->service->create($request->validated(), auth()->id());
            return $this->apiSuccess($category, 'Category created successfully', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create category', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->findOrFail($id));
        } catch (\Throwable $e) {
            return $this->apiError('Category not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate(['name' => 'required|string|max:255', 'branch_id' => 'nullable|integer']);
            $category = $this->service->update($id, $request->only(['name', 'branch_id']));
            return $this->apiSuccess($category, 'Category updated successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update category', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return $this->apiSuccess(null, 'Category deleted successfully');
        } catch (\DomainException $e) {
            return $this->apiError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete category', 500, $e->getMessage());
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate(['ids' => 'required|array']);
            $count = $this->service->bulkDelete($request->ids);
            return $this->apiSuccess(null, "{$count} categories deleted successfully");
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete categories', 500, $e->getMessage());
        }
    }
}
