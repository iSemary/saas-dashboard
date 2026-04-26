<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Infrastructure\Persistence\HolidayRepositoryInterface;

class HolidayApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected HolidayRepositoryInterface $repository,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $holidays = $this->repository->paginate(
            filters: $request->only(['search', 'year', 'country', 'is_recurring']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $holidays);
    }

    public function store(Request $request): JsonResponse
    {
        $holiday = $this->repository->create($request->validated());
        return $this->success(data: $holiday, message: translate('message.action_completed'));
    }

    public function show(int $id): JsonResponse
    {
        $holiday = $this->repository->findOrFail($id);
        return $this->success(data: $holiday);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $holiday = $this->repository->update($id, $request->validated());
        return $this->success(data: $holiday, message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function byYear(Request $request, int $year): JsonResponse
    {
        $holidays = $this->repository->getHolidaysByYear($year, $request->input('country'));
        return $this->success(data: $holidays);
    }

    public function check(Request $request): JsonResponse
    {
        $isHoliday = $this->repository->isHoliday(
            $request->input('date'),
            $request->input('department_id')
        );
        return $this->success(data: ['is_holiday' => $isHoliday]);
    }
}
