<?php
declare(strict_types=1);
namespace Modules\Expenses\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Expenses\Presentation\Http\Requests\StoreExpenseRequest;
use Modules\Expenses\Presentation\Http\Requests\UpdateExpenseRequest;
use Illuminate\Routing\Controller;
use Modules\Expenses\Infrastructure\Persistence\ExpenseRepositoryInterface;
use Modules\Expenses\Application\UseCases\SubmitExpense;
use Modules\Expenses\Application\UseCases\ApproveExpense;
use Modules\Expenses\Application\UseCases\RejectExpense;

class ExpenseController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected ExpenseRepositoryInterface $repository,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(StoreExpenseRequest $request): JsonResponse
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

    public function update(UpdateExpenseRequest $request, int $id): JsonResponse
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
        return $this->apiSuccess(['deleted' => $count], "{$count} Expense(s) deleted successfully");
    }

    public function submit(SubmitExpense $useCase, int $id): JsonResponse
    {
        $item = $useCase->execute($id);
        return $this->apiSuccess($item, translate('message.action_completed'));
    }

    public function approve(ApproveExpense $useCase, int $id): JsonResponse
    {
        $item = $useCase->execute($id);
        return $this->apiSuccess($item, translate('message.action_completed'));
    }

    public function reject(RejectExpense $useCase, int $id, Request $request): JsonResponse
    {
        $reason = $request->input('reason', '');
        $item = $useCase->execute($id, $reason);
        return $this->apiSuccess($item, translate('message.action_completed'));
    }
}
