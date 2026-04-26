<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\DTOs\CreateDepartmentData;
use Modules\HR\Application\DTOs\UpdateDepartmentData;
use Modules\HR\Application\UseCases\Department\CreateDepartmentUseCase;
use Modules\HR\Application\UseCases\Department\DeleteDepartmentUseCase;
use Modules\HR\Application\UseCases\Department\UpdateDepartmentUseCase;
use Modules\HR\Infrastructure\Persistence\DepartmentRepositoryInterface;
use Modules\HR\Presentation\Http\Requests\StoreDepartmentRequest;
use Modules\HR\Presentation\Http\Requests\UpdateDepartmentRequest;

class DepartmentApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected DepartmentRepositoryInterface $repository,
        protected CreateDepartmentUseCase $createUseCase,
        protected UpdateDepartmentUseCase $updateUseCase,
        protected DeleteDepartmentUseCase $deleteUseCase,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $departments = $this->repository->paginate(
            filters: $request->only(['search', 'status', 'parent_id']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $departments);
    }

    public function tree(Request $request): JsonResponse
    {
        $tree = $this->repository->getTree();
        return $this->success(data: $tree);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        try {
            $data = CreateDepartmentData::fromRequest($request);
            $department = $this->createUseCase->execute($data);
            return $this->success(
                message: translate('message.action_completed'),
                data: $department->fresh(['parent', 'manager'])
            );
        } catch (\Exception $e) {
            return $this->error(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $department = $this->repository->findOrFail($id);
            return $this->success(data: $department);
        } catch (\Exception $e) {
            return $this->notFound('Department not found');
        }
    }

    public function update(UpdateDepartmentRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdateDepartmentData::fromRequest($request);
            $department = $this->updateUseCase->execute($id, $data);
            return $this->success(
                message: translate('message.action_completed'),
                data: $department->fresh(['parent', 'manager'])
            );
        } catch (\Exception $e) {
            return $this->error(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteUseCase->execute($id);
            return $this->success(message: translate('message.action_completed'));
        } catch (\Exception $e) {
            return $this->error(message: $e->getMessage(), code: 422);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        $count = $this->repository->bulkDelete($request->input('ids'));
        return $this->success(message: "{$count} departments deleted successfully");
    }
}
