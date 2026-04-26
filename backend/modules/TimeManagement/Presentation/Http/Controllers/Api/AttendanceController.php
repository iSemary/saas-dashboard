<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\AttendanceRepositoryInterface;

class AttendanceController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected AttendanceRepositoryInterface $repository) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        return $this->apiPaginated($this->repository->paginateByUser(
            $request->user()->id,
            $filters,
            $request->get('per_page', 15)
        ));
    }

    public function show(string $id): JsonResponse
    {
        return $this->apiSuccess($this->repository->findOrFail($id));
    }

    public function clockIn(Request $request): JsonResponse
    {
        $attendance = $this->repository->create([
            'tenant_id' => $request->user()->tenant_id ?? '',
            'user_id' => $request->user()->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->toDateTimeString(),
            'status' => 'present',
        ]);
        return $this->apiSuccess($attendance, translate('message.action_completed'), 201);
    }

    public function clockOut(Request $request): JsonResponse
    {
        $attendance = $this->repository->findActiveClockIn(
            $request->user()->id,
            now()->toDateString()
        );

        $attendance->clockOut();
        return $this->apiSuccess($attendance->fresh(), translate('message.action_completed'));
    }
}
