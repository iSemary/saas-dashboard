<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Infrastructure\Persistence\ProjectRepositoryInterface;
use Modules\ProjectManagement\Application\DTOs\CreateProjectData;
use Modules\ProjectManagement\Application\DTOs\UpdateProjectData;
use Modules\ProjectManagement\Application\UseCases\Project\CreateProject;
use Modules\ProjectManagement\Application\UseCases\Project\UpdateProject;
use Modules\ProjectManagement\Application\UseCases\Project\ChangeProjectStatus;
use Modules\ProjectManagement\Domain\ValueObjects\ProjectStatus;

class ProjectController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected ProjectRepositoryInterface $repository,
        protected CreateProject $createProject,
        protected UpdateProject $updateProject,
        protected ChangeProjectStatus $changeStatus,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateProjectData::fromRequest($request);
        $project = $this->createProject->execute($data, $request->user()->id);
        return $this->apiSuccess($project, translate('message.created_successfully'), 201);
    }

    public function show(string $id): JsonResponse
    {
        $project = $this->repository->findOrFail($id);
        return $this->apiSuccess($project);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = UpdateProjectData::fromRequest($request);
        $project = $this->updateProject->execute($id, $data);
        return $this->apiSuccess($project, translate('message.updated_successfully'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function archive(string $id): JsonResponse
    {
        $project = $this->changeStatus->execute($id, ProjectStatus::ARCHIVED);
        return $this->apiSuccess($project, translate('message.action_completed'));
    }

    public function pause(string $id): JsonResponse
    {
        $project = $this->changeStatus->execute($id, ProjectStatus::ON_HOLD);
        return $this->apiSuccess($project, translate('message.action_completed'));
    }

    public function complete(string $id): JsonResponse
    {
        $project = $this->changeStatus->execute($id, ProjectStatus::COMPLETED);
        return $this->apiSuccess($project, translate('message.action_completed'));
    }

    public function recalculateHealth(Request $request, string $id): JsonResponse
    {
        $project = $this->repository->findOrFail($id);
        $project->recalculateHealth($request->input('score', 0.0));
        return $this->apiSuccess($project->fresh(), translate('message.action_completed'));
    }

    public function createFromTemplate(Request $request, string $id): JsonResponse
    {
        $template = \Modules\ProjectManagement\Domain\Entities\ProjectTemplate::findOrFail($id);
        $data = CreateProjectData::fromRequest($request);
        $project = $this->createProject->execute($data, $request->user()->id);
        return $this->apiSuccess($project, translate('message.created_successfully'), 201);
    }
}
