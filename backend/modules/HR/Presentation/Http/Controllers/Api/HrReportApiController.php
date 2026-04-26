<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\LeaveRequestRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\AttendanceRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\PayrollRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\ApplicationRepositoryInterface;

class HrReportApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected EmployeeRepositoryInterface $employeeRepository,
        protected LeaveRequestRepositoryInterface $leaveRepository,
        protected AttendanceRepositoryInterface $attendanceRepository,
        protected PayrollRepositoryInterface $payrollRepository,
        protected ApplicationRepositoryInterface $applicationRepository,
    ) {}

    public function headcount(): JsonResponse
    {
        return $this->success(data: [
            'total' => $this->employeeRepository->count(),
            'active' => $this->employeeRepository->count(['employment_status' => 'active']),
            'terminated' => $this->employeeRepository->count(['employment_status' => 'terminated']),
        ]);
    }

    public function leaveUsage(): JsonResponse
    {
        return $this->success(data: $this->leaveRepository->getCountByStatus());
    }

    public function attendanceSummary(): JsonResponse
    {
        return $this->success(data: $this->attendanceRepository->getCountByStatus());
    }

    public function payrollSummary(): JsonResponse
    {
        return $this->success(data: $this->payrollRepository->getSummary());
    }

    public function recruitmentFunnel(): JsonResponse
    {
        return $this->success(data: $this->applicationRepository->getCountByStatus());
    }
}
