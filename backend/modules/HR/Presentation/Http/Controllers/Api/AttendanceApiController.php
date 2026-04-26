<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\DTOs\ApproveAttendanceData;
use Modules\HR\Application\DTOs\CheckInData;
use Modules\HR\Application\DTOs\CheckOutData;
use Modules\HR\Application\UseCases\Attendance\ApproveAttendanceUseCase;
use Modules\HR\Application\UseCases\Attendance\CheckInUseCase;
use Modules\HR\Application\UseCases\Attendance\CheckOutUseCase;
use Modules\HR\Infrastructure\Persistence\AttendanceRepositoryInterface;

class AttendanceApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected AttendanceRepositoryInterface $repository,
        protected CheckInUseCase $checkInUseCase,
        protected CheckOutUseCase $checkOutUseCase,
        protected ApproveAttendanceUseCase $approveAttendanceUseCase,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $attendances = $this->repository->paginate(
            filters: $request->only(['search', 'employee_id', 'status', 'start_date', 'end_date', 'is_approved']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $attendances);
    }

    public function store(Request $request): JsonResponse
    {
        $attendance = $this->repository->create($request->validated());
        return $this->success(data: $attendance, message: translate('message.action_completed'));
    }

    public function show(int $id): JsonResponse
    {
        $attendance = $this->repository->findOrFail($id);
        return $this->success(data: $attendance);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $attendance = $this->repository->update($id, $request->validated());
        return $this->success(data: $attendance, message: translate('message.action_completed'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->success(message: translate('message.action_completed'));
    }

    public function checkIn(Request $request): JsonResponse
    {
        $data = new CheckInData(
            employeeId: $request->input('employee_id'),
            ipAddress: $request->ip(),
            latitude: $request->input('latitude'),
            longitude: $request->input('longitude'),
            notes: $request->input('notes'),
        );
        
        $attendance = $this->checkInUseCase->execute($data);
        return $this->success(data: $attendance, message: translate('message.action_completed'));
    }

    public function checkOut(Request $request, int $id): JsonResponse
    {
        $data = new CheckOutData(
            attendanceId: $id,
            notes: $request->input('notes'),
        );
        
        $attendance = $this->checkOutUseCase->execute($data);
        return $this->success(data: $attendance, message: translate('message.action_completed'));
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $data = new ApproveAttendanceData(
            attendanceId: $id,
            approved: $request->boolean('approved', true),
            notes: $request->input('notes'),
        );
        
        $attendance = $this->approveAttendanceUseCase->execute($data);
        return $this->success(data: $attendance, message: translate('message.action_completed'));
    }

    public function pendingApprovals(): JsonResponse
    {
        $attendances = $this->repository->getPendingApprovals();
        return $this->success(data: $attendances);
    }

    public function today(int $employeeId): JsonResponse
    {
        $attendance = $this->repository->getTodayAttendance($employeeId);
        return $this->success(data: $attendance);
    }

    public function byDateRange(Request $request, int $employeeId): JsonResponse
    {
        $attendances = $this->repository->getAttendanceByDateRange(
            $employeeId,
            $request->input('start_date'),
            $request->input('end_date')
        );
        return $this->success(data: $attendances);
    }
}
