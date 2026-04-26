<?php
declare(strict_types=1);
namespace Modules\Expenses\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Expenses\Presentation\Http\Requests\StoreExpenseCategoryRequest;
use Modules\Expenses\Presentation\Http\Requests\UpdateExpenseCategoryRequest;
use Illuminate\Routing\Controller;
use Modules\Expenses\Infrastructure\Persistence\ExpenseCategoryRepositoryInterface;

class ExpenseCategoryController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected ExpenseCategoryRepositoryInterface $repository,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(StoreExpenseCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $item = $this->repository->create($data);
        return $this->apiSuccess($item, translate('message.created_successfully'), 201);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->repository->findOrFail($id);
        return $this->apiSuccess($item);
    }

    public function update(UpdateExpenseCategoryRequest $request, int $id): JsonResponse
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
        return $this->apiSuccess(['deleted' => $count], "${count} ExpenseCategory(s) deleted successfully");
    }
}
