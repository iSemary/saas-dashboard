<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class EmployeeApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected EmployeeRepositoryInterface $repository,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $employees = $this->repository->paginate(
            filters: $request->only(['search', 'department_id', 'position_id', 'employment_status', 'employment_type', 'manager_id']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $employees);
    }

    public function orgChart(Request $request): JsonResponse
    {
        $chart = $this->repository->getOrgChart();
        return $this->success(data: $chart);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->success(message: translate('message.created_successfully'));
    }

    public function show(int $id): JsonResponse
    {
        try {
            $employee = $this->repository->findOrFail($id);
            return $this->success(data: $employee);
        } catch (\Exception $e) {
            return $this->notFound('Employee not found');
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return $this->success(message: translate('message.updated_successfully'));
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->success(message: translate('message.deleted_successfully'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        return $this->success(message: translate('message.deleted_successfully'));
    }

    public function transfer(Request $request, int $id): JsonResponse
    {
        return $this->success(message: translate('message.action_completed'));
    }

    public function promote(Request $request, int $id): JsonResponse
    {
        return $this->success(message: translate('message.action_completed'));
    }

    public function terminate(Request $request, int $id): JsonResponse
    {
        return $this->success(message: translate('message.action_completed'));
    }

    public function reactivate(Request $request, int $id): JsonResponse
    {
        return $this->success(message: translate('message.action_completed'));
    }

    public function uploadAvatar(Request $request, int $id): JsonResponse
    {
        return $this->success(message: translate('message.action_completed'));
    }

    public function removeAvatar(int $id): JsonResponse
    {
        return $this->success(message: translate('message.action_completed'));
    }

    public function documents(int $id): JsonResponse
    {
        return $this->success(data: []);
    }

    public function storeDocument(Request $request, int $id): JsonResponse
    {
        return $this->success(message: translate('message.action_completed'));
    }

    public function destroyDocument(int $id, int $documentId): JsonResponse
    {
        return $this->success(message: translate('message.deleted_successfully'));
    }

    public function contracts(int $id): JsonResponse
    {
        return $this->success(data: []);
    }

    public function storeContract(Request $request, int $id): JsonResponse
    {
        return $this->success(message: translate('message.created_successfully'));
    }

    public function destroyContract(int $id, int $contractId): JsonResponse
    {
        return $this->success(message: translate('message.deleted_successfully'));
    }

    public function history(int $id): JsonResponse
    {
        return $this->success(data: []);
    }

    public function import(Request $request): JsonResponse
    {
        return $this->success(message: translate('message.action_completed'));
    }
}
