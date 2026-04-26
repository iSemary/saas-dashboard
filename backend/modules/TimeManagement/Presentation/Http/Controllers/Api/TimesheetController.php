<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\TimesheetRepositoryInterface;
use Modules\TimeManagement\Application\DTOs\CreateTimesheetData;
use Modules\TimeManagement\Application\UseCases\Timesheet\CreateTimesheet;
use Modules\TimeManagement\Application\UseCases\Timesheet\SubmitTimesheet;
use Modules\TimeManagement\Application\UseCases\Timesheet\ApproveTimesheet;
use Modules\TimeManagement\Application\UseCases\Timesheet\RejectTimesheet;

class TimesheetController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected TimesheetRepositoryInterface $repository,
        protected CreateTimesheet $createTimesheet,
        protected SubmitTimesheet $submitTimesheet,
        protected ApproveTimesheet $approveTimesheet,
        protected RejectTimesheet $rejectTimesheet,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateTimesheetData::fromRequest($request);
        $ts = $this->createTimesheet->execute($data);
        return $this->apiSuccess($ts, translate('message.created_successfully'), 201);
    }

    public function show(string $id): JsonResponse
    {
        return $this->apiSuccess($this->repository->findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $ts = $this->repository->update($id, $request->all());
        return $this->apiSuccess($ts, translate('message.updated_successfully'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function submit(string $id): JsonResponse
    {
        $ts = $this->submitTimesheet->execute($id, request()->user()->id);
        return $this->apiSuccess($ts, translate('message.action_completed'));
    }

    public function approve(string $id): JsonResponse
    {
        $ts = $this->approveTimesheet->execute($id, request()->user()->id);
        return $this->apiSuccess($ts, translate('message.action_completed'));
    }

    public function reject(Request $request, string $id): JsonResponse
    {
        $ts = $this->rejectTimesheet->execute($id, request()->user()->id, $request->input('reason', ''));
        return $this->apiSuccess($ts, translate('message.action_completed'));
    }

    public function autoGenerate(Request $request): JsonResponse
    {
        $data = CreateTimesheetData::fromRequest($request);
        $ts = $this->createTimesheet->execute($data);
        return $this->apiSuccess($ts, translate('message.action_completed'), 201);
    }
}
