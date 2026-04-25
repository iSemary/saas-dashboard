<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Infrastructure\Persistence\LeaveTypeRepositoryInterface;

class LeaveTypeApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected LeaveTypeRepositoryInterface $repository,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $leaveTypes = $this->repository->paginate(
            filters: $request->only(['search', 'is_active', 'is_paid']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $leaveTypes);
    }

    public function store(Request $request): JsonResponse
    {
        $leaveType = $this->repository->create($request->validated());
        return $this->success(data: $leaveType, message: 'Leave type created successfully');
    }

    public function show(int $id): JsonResponse
    {
        $leaveType = $this->repository->findOrFail($id);
        return $this->success(data: $leaveType);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $leaveType = $this->repository->update($id, $request->validated());
        return $this->success(data: $leaveType, message: 'Leave type updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->success(message: 'Leave type deleted successfully');
    }

    public function active(): JsonResponse
    {
        $leaveTypes = $this->repository->getActiveLeaveTypes();
        return $this->success(data: $leaveTypes);
    }
}
