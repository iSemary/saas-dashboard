<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Infrastructure\Persistence\PerformanceCycleRepositoryInterface;

class PerformanceCycleApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected PerformanceCycleRepositoryInterface $repository,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $cycles = $this->repository->paginate(
            filters: $request->only(['search', 'status']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $cycles);
    }

    public function store(Request $request): JsonResponse
    {
        $cycle = $this->repository->create($request->validated());
        return $this->success(data: $cycle, message: translate('message.action_completed'));
    }

    public function show(int $id): JsonResponse
    {
        $cycle = $this->repository->findOrFail($id);
        return $this->success(data: $cycle);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $cycle = $this->repository->update($id, $request->validated());
        return $this->success(data: $cycle, message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function active(): JsonResponse
    {
        $cycles = $this->repository->getActive();
        return $this->success(data: $cycles);
    }

    public function open(): JsonResponse
    {
        $cycles = $this->repository->getOpen();
        return $this->success(data: $cycles);
    }
}
