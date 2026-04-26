<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Infrastructure\Persistence\IssueRepositoryInterface;
use Modules\ProjectManagement\Infrastructure\Persistence\TaskRepositoryInterface;

class ProjectIssueController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected IssueRepositoryInterface $repository,
        protected TaskRepositoryInterface $taskRepository,
    ) {}

    public function index(Request $request, string $projectId): JsonResponse
    {
        return $this->apiSuccess($this->repository->getByProject($projectId));
    }

    public function store(Request $request, string $projectId): JsonResponse
    {
        $item = $this->repository->create(array_merge($request->all(), ['project_id' => $projectId, 'created_by' => $request->user()->id]));
        return $this->apiSuccess($item, translate('message.created_successfully'), 201);
    }

    public function show(string $projectId, string $id): JsonResponse
    {
        return $this->apiSuccess($this->repository->findOrFail($id));
    }

    public function update(Request $request, string $projectId, string $id): JsonResponse
    {
        $item = $this->repository->update($id, $request->all());
        return $this->apiSuccess($item, translate('message.updated_successfully'));
    }

    public function destroy(string $projectId, string $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function promoteToTask(Request $request, string $id): JsonResponse
    {
        $issue = $this->repository->findOrFail($id);
        $task = $this->taskRepository->create([
            'tenant_id' => $issue->tenant_id,
            'project_id' => $issue->project_id,
            'title' => $issue->title,
            'description' => $issue->description,
            'priority' => $issue->severity ?? 'medium',
            'assignee_id' => $issue->assignee_id,
            'created_by' => $request->user()->id,
        ]);
        $this->repository->delete($id);
        return $this->apiSuccess($task, translate('message.action_completed'), 201);
    }
}
