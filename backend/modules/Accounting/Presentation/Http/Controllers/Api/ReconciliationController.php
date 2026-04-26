<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Accounting\Presentation\Http\Requests\StoreReconciliationRequest;
use Modules\Accounting\Presentation\Http\Requests\UpdateReconciliationRequest;
use Illuminate\Routing\Controller;
use Modules\Accounting\Infrastructure\Persistence\ReconciliationRepositoryInterface;

class ReconciliationController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected ReconciliationRepositoryInterface $repository,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(StoreReconciliationRequest $request): JsonResponse
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

    public function update(UpdateReconciliationRequest $request, int $id): JsonResponse
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
        return $this->apiSuccess(['deleted' => $count], "${count} Reconciliation(s) deleted successfully");
    }

    public function complete(int $id): JsonResponse
    {
        $recon = $this->repository->findOrFail($id);
        $recon->complete();
        return $this->apiSuccess($recon->fresh(), "Reconciliation completed successfully");
    }

}
