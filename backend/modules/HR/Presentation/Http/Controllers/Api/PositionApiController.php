<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\DTOs\CreatePositionData;
use Modules\HR\Application\DTOs\UpdatePositionData;
use Modules\HR\Application\UseCases\Position\CreatePositionUseCase;
use Modules\HR\Application\UseCases\Position\DeletePositionUseCase;
use Modules\HR\Application\UseCases\Position\UpdatePositionUseCase;
use Modules\HR\Infrastructure\Persistence\PositionRepositoryInterface;
use Modules\HR\Presentation\Http\Requests\StorePositionRequest;
use Modules\HR\Presentation\Http\Requests\UpdatePositionRequest;

class PositionApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected PositionRepositoryInterface $repository,
        protected CreatePositionUseCase $createUseCase,
        protected UpdatePositionUseCase $updateUseCase,
        protected DeletePositionUseCase $deleteUseCase,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $positions = $this->repository->paginate(
            filters: $request->only(['search', 'department_id', 'level', 'is_active']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $positions);
    }

    public function store(StorePositionRequest $request): JsonResponse
    {
        try {
            $data = CreatePositionData::fromRequest($request);
            $position = $this->createUseCase->execute($data);
            return $this->success(
                message: translate('message.action_completed'),
                data: $position->fresh(['department'])
            );
        } catch (\Exception $e) {
            return $this->error(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $position = $this->repository->findOrFail($id);
            return $this->success(data: $position);
        } catch (\Exception $e) {
            return $this->notFound('Position not found');
        }
    }

    public function update(UpdatePositionRequest $request, int $id): JsonResponse
    {
        try {
            $data = UpdatePositionData::fromRequest($request);
            $position = $this->updateUseCase->execute($id, $data);
            return $this->success(
                message: translate('message.action_completed'),
                data: $position->fresh(['department'])
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
        return $this->success(message: "{$count} positions deleted successfully");
    }

    public function byDepartment(int $departmentId): JsonResponse
    {
        $positions = $this->repository->getByDepartment($departmentId);
        return $this->success(data: $positions);
    }
}
