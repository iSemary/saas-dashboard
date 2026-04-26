<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Infrastructure\Persistence\TaskDependencyRepositoryInterface;

class TaskDependencyController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected TaskDependencyRepositoryInterface $repository) {}

    public function index(Request $request, string $taskId): JsonResponse
    {
        return $this->apiSuccess($this->repository->getByTask($taskId));
    }

    public function store(Request $request, string $taskId): JsonResponse
    {
        $item = $this->repository->create($request->all());
        return $this->apiSuccess($item, translate('message.created_successfully'), 201);
    }

    public function destroy(string $taskId, string $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }
}
