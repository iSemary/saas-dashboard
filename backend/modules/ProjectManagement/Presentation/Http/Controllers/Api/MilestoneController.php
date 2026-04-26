<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Infrastructure\Persistence\MilestoneRepositoryInterface;

class MilestoneController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected MilestoneRepositoryInterface $repository) {}

    public function index(Request $request, string $projectId): JsonResponse
    {
        $filters = array_merge($request->only(['status', 'search']), ['project_id' => $projectId]);
        $result = $this->repository->paginate($filters, $request->get('per_page', 15));
        return $this->apiPaginated($result);
    }

    public function store(Request $request, string $projectId): JsonResponse
    {
        $item = $this->repository->create(array_merge($request->all(), ['project_id' => $projectId, 'created_by' => $request->user()->id]));
        return $this->apiSuccess($item, translate('message.created_successfully'), 201);
    }

    public function show(string $projectId, string $milestoneId): JsonResponse
    {
        return $this->apiSuccess($this->repository->findOrFail($milestoneId));
    }

    public function update(Request $request, string $projectId, string $milestoneId): JsonResponse
    {
        return $this->apiSuccess($this->repository->update($milestoneId, $request->all()), translate('message.updated_successfully'));
    }

    public function destroy(string $projectId, string $milestoneId): JsonResponse
    {
        $this->repository->delete($milestoneId);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
