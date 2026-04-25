<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\DTOs\ApproveLeaveData;
use Modules\HR\Application\DTOs\RejectLeaveData;
use Modules\HR\Application\DTOs\RequestLeaveData;
use Modules\HR\Application\UseCases\Leave\ApproveLeaveUseCase;
use Modules\HR\Application\UseCases\Leave\CancelLeaveUseCase;
use Modules\HR\Application\UseCases\Leave\RejectLeaveUseCase;
use Modules\HR\Application\UseCases\Leave\RequestLeaveUseCase;
use Modules\HR\Infrastructure\Persistence\LeaveRequestRepositoryInterface;

class LeaveRequestApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected LeaveRequestRepositoryInterface $repository,
        protected RequestLeaveUseCase $requestLeaveUseCase,
        protected ApproveLeaveUseCase $approveLeaveUseCase,
        protected RejectLeaveUseCase $rejectLeaveUseCase,
        protected CancelLeaveUseCase $cancelLeaveUseCase,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $leaveRequests = $this->repository->paginate(
            filters: $request->only(['search', 'employee_id', 'leave_type_id', 'status', 'start_date', 'end_date']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $leaveRequests);
    }

    public function store(Request $request): JsonResponse
    {
        $data = new RequestLeaveData(
            employeeId: $request->input('employee_id'),
            leaveTypeId: $request->input('leave_type_id'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            isHalfDay: $request->boolean('is_half_day', false),
            halfDaySession: $request->input('half_day_session'),
            reason: $request->input('reason'),
        );
        
        $leaveRequest = $this->requestLeaveUseCase->execute($data);
        return $this->success(data: $leaveRequest, message: 'Leave request submitted successfully');
    }

    public function show(int $id): JsonResponse
    {
        $leaveRequest = $this->repository->findOrFail($id);
        return $this->success(data: $leaveRequest);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $data = new ApproveLeaveData(
            leaveRequestId: $id,
            notes: $request->input('notes'),
        );
        
        $leaveRequest = $this->approveLeaveUseCase->execute($data);
        return $this->success(data: $leaveRequest, message: 'Leave request approved successfully');
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $data = new RejectLeaveData(
            leaveRequestId: $id,
            reason: $request->input('reason', ''),
        );
        
        $leaveRequest = $this->rejectLeaveUseCase->execute($data);
        return $this->success(data: $leaveRequest, message: 'Leave request rejected successfully');
    }

    public function cancel(int $id): JsonResponse
    {
        $leaveRequest = $this->cancelLeaveUseCase->execute($id);
        return $this->success(data: $leaveRequest, message: 'Leave request cancelled successfully');
    }

    public function pending(): JsonResponse
    {
        $leaveRequests = $this->repository->getPendingRequests();
        return $this->success(data: $leaveRequests);
    }

    public function byEmployee(Request $request, int $employeeId): JsonResponse
    {
        $leaveRequests = $this->repository->getRequestsByEmployee(
            $employeeId,
            $request->input('status')
        );
        return $this->success(data: $leaveRequests);
    }

    public function checkOverlap(Request $request): JsonResponse
    {
        $hasOverlap = $this->repository->hasOverlappingLeave(
            $request->input('employee_id'),
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('exclude_id')
        );
        return $this->success(data: ['has_overlap' => $hasOverlap]);
    }
}
