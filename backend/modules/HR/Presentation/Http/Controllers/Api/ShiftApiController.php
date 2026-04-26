<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Infrastructure\Persistence\ShiftRepositoryInterface;

class ShiftApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected ShiftRepositoryInterface $repository,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $shifts = $this->repository->paginate(
            filters: $request->only(['search', 'is_active']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $shifts);
    }

    public function store(Request $request): JsonResponse
    {
        $shift = $this->repository->create($request->validated());
        return $this->success(data: $shift, message: translate('message.action_completed'));
    }

    public function show(int $id): JsonResponse
    {
        $shift = $this->repository->findOrFail($id);
        return $this->success(data: $shift);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $shift = $this->repository->update($id, $request->validated());
        return $this->success(data: $shift, message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function active(): JsonResponse
    {
        $shifts = $this->repository->getActiveShifts();
        return $this->success(data: $shifts);
    }
}
