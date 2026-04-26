<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Infrastructure\Persistence\TaskRepositoryInterface;
use Modules\ProjectManagement\Application\DTOs\CreateTaskData;
use Modules\ProjectManagement\Application\DTOs\UpdateTaskData;
use Modules\ProjectManagement\Application\UseCases\Task\CreateTask;
use Modules\ProjectManagement\Application\UseCases\Task\UpdateTask;
use Modules\ProjectManagement\Application\UseCases\Task\ChangeTaskStatus;
use Modules\ProjectManagement\Application\UseCases\Task\MoveTaskToColumn;
use Modules\ProjectManagement\Application\UseCases\Task\ReorderTask;
use Modules\ProjectManagement\Domain\ValueObjects\TaskStatus;

class TaskController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected TaskRepositoryInterface $repository,
        protected CreateTask $createTask,
        protected UpdateTask $updateTask,
        protected ChangeTaskStatus $changeStatus,
        protected MoveTaskToColumn $moveToColumn,
        protected ReorderTask $reorderTask,
    ) {}

    public function index(Request $request, string $projectId): JsonResponse
    {
        $filters = array_merge($request->only(['status', 'priority', 'assignee_id', 'milestone_id', 'search']), ['project_id' => $projectId]);
        $perPage = $request->get('per_page', 15);
        $result = $this->repository->paginate($filters, $perPage);
        return $this->apiPaginated($result);
    }

    public function store(Request $request, string $projectId): JsonResponse
    {
        $data = CreateTaskData::fromRequest($request, $projectId);
        $task = $this->createTask->execute($data, $request->user()->id);
        return $this->apiSuccess($task, translate('message.created_successfully'), 201);
    }

    public function show(string $projectId, string $taskId): JsonResponse
    {
        $task = $this->repository->findOrFail($taskId);
        return $this->apiSuccess($task);
    }

    public function update(Request $request, string $projectId, string $taskId): JsonResponse
    {
        $data = UpdateTaskData::fromRequest($request);
        $task = $this->updateTask->execute($taskId, $data);
        return $this->apiSuccess($task, translate('message.updated_successfully'));
    }

    public function destroy(string $projectId, string $taskId): JsonResponse
    {
        $this->repository->delete($taskId);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function move(Request $request, string $id): JsonResponse
    {
        $task = $this->moveToColumn->execute($id, $request->input('column_id'), $request->input('position', 0));
        return $this->apiSuccess($task, translate('message.action_completed'));
    }

    public function reorder(Request $request, string $id): JsonResponse
    {
        $task = $this->reorderTask->execute($id, $request->input('position'));
        return $this->apiSuccess($task, translate('message.action_completed'));
    }

    public function attachLabels(Request $request, string $id): JsonResponse
    {
        $task = $this->repository->findOrFail($id);
        $task->labels()->syncWithoutDetaching($request->input('label_ids', []));
        return $this->apiSuccess($task->fresh()->load('labels'), translate('message.action_completed'));
    }

    public function detachLabels(Request $request, string $id): JsonResponse
    {
        $task = $this->repository->findOrFail($id);
        $task->labels()->detach($request->input('label_ids', []));
        return $this->apiSuccess($task->fresh()->load('labels'), translate('message.action_completed'));
    }
}
