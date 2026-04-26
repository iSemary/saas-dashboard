<?php
declare(strict_types=1);
namespace Modules\Expenses\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Expenses\Presentation\Http\Requests\StoreExpenseReportRequest;
use Modules\Expenses\Presentation\Http\Requests\UpdateExpenseReportRequest;
use Illuminate\Routing\Controller;
use Modules\Expenses\Infrastructure\Persistence\ExpenseReportRepositoryInterface;
use Modules\Expenses\Application\UseCases\SubmitReport;

class ExpenseReportController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected ExpenseReportRepositoryInterface $repository,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(StoreExpenseReportRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $item = $this->repository->create($data);
        return $this->apiSuccess($item, translate('message.created_successfully'), 201);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->repository->findOrFail($id);
        return $this->apiSuccess($item);
    }

    public function update(UpdateExpenseReportRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $item = $this->repository->update($id, $data);
        return $this->apiSuccess($item, translate('message.updated_successfully'));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        $count = $this->repository->bulkDelete($ids);
        return $this->apiSuccess(['deleted' => $count], "{$count} ExpenseReport(s) deleted successfully");
    }

    public function submit(SubmitReport $useCase, int $id): JsonResponse
    {
        $item = $useCase->execute($id);
        return $this->apiSuccess($item, translate('message.action_completed'));
    }

    public function approve(int $id): JsonResponse
    {
        $report = $this->repository->findOrFail($id);
        $report->approve(auth()->id());
        return $this->apiSuccess($report->fresh(), translate('message.action_completed'));
    }

    public function reject(int $id, Request $request): JsonResponse
    {
        $report = $this->repository->findOrFail($id);
        $report->reject(auth()->id(), $request->input('reason', ''));
        return $this->apiSuccess($report->fresh(), translate('message.action_completed'));
    }
}
