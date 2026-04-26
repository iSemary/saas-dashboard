<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\LeaveRequestRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\AttendanceRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\PayrollRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\GoalRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\AssetAssignmentRepositoryInterface;

class SelfServiceApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected EmployeeRepositoryInterface $employeeRepository,
        protected LeaveRequestRepositoryInterface $leaveRepository,
        protected AttendanceRepositoryInterface $attendanceRepository,
        protected PayrollRepositoryInterface $payrollRepository,
        protected GoalRepositoryInterface $goalRepository,
        protected AssetAssignmentRepositoryInterface $assetRepository,
    ) {}

    private function employee(Request $request)
    {
        return $request->attributes->get('employee_context')
            ?? $this->employeeRepository->findByUserId(auth()->id());
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(data: $this->employee($request));
    }

    public function leaves(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: $this->leaveRepository->getRequestsByEmployee($employee->id));
    }

    public function attendance(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: $this->attendanceRepository->getAttendanceByDateRange(
            $employee->id,
            now()->subDays(31)->toDateString(),
            now()->toDateString()
        ));
    }

    public function payroll(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: $this->payrollRepository->paginate(12, ['employee_id' => $employee->id]));
    }

    public function goals(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: $this->goalRepository->paginate(20, ['employee_id' => $employee->id]));
    }

    public function assets(Request $request): JsonResponse
    {
        $employee = $this->employee($request);
        return $this->success(data: $this->assetRepository->paginate(20, ['employee_id' => $employee->id]));
    }
}
