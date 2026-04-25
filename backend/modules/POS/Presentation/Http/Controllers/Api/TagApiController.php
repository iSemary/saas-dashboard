<?php

namespace Modules\POS\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\POS\Application\Services\TagService;

class TagApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly TagService $service) {}

    public function index(Request $request): JsonResponse
    {
        try {
            return $this->apiSuccess($this->service->all($request->only(['type'])));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve tags', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate(['type' => 'required|string', 'value' => 'required|string|max:255']);
            $tag = $this->service->create($request->only(['type', 'value']), auth()->id());
            return $this->apiSuccess($tag, 'Tag created successfully', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create tag', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return $this->apiSuccess(null, 'Tag deleted successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete tag', 500, $e->getMessage());
        }
    }
}
