<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Infrastructure\Persistence\WorkScheduleRepositoryInterface;

class WorkScheduleApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected WorkScheduleRepositoryInterface $repository,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $schedules = $this->repository->paginate(
            filters: $request->only(['search', 'employee_id', 'shift_id']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $schedules);
    }

    public function store(Request $request): JsonResponse
    {
        $schedule = $this->repository->create($request->validated());
        return $this->success(data: $schedule, message: 'Work schedule created successfully');
    }

    public function show(int $id): JsonResponse
    {
        $schedule = $this->repository->findOrFail($id);
        return $this->success(data: $schedule);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $schedule = $this->repository->update($id, $request->validated());
        return $this->success(data: $schedule, message: 'Work schedule updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->success(message: 'Work schedule deleted successfully');
    }

    public function current(int $employeeId): JsonResponse
    {
        $schedule = $this->repository->getCurrentScheduleForEmployee($employeeId);
        return $this->success(data: $schedule);
    }

    public function byEmployee(int $employeeId): JsonResponse
    {
        $schedules = $this->repository->getSchedulesByEmployee($employeeId);
        return $this->success(data: $schedules);
    }
}
