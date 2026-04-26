<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Accounting\Presentation\Http\Requests\StoreFiscalYearRequest;
use Modules\Accounting\Presentation\Http\Requests\UpdateFiscalYearRequest;
use Illuminate\Routing\Controller;
use Modules\Accounting\Infrastructure\Persistence\FiscalYearRepositoryInterface;

class FiscalYearController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected FiscalYearRepositoryInterface $repository,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(StoreFiscalYearRequest $request): JsonResponse
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

    public function update(UpdateFiscalYearRequest $request, int $id): JsonResponse
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
        return $this->apiSuccess(['deleted' => $count], "${count} FiscalYear(s) deleted successfully");
    }

    public function close(int $id): JsonResponse
    {
        $fy = $this->repository->findOrFail($id);
        $fy->transitionStatus(\Modules\Accounting\Domain\ValueObjects\FiscalYearStatus::CLOSED);
        return $this->apiSuccess($fy->fresh(), "Fiscal year closed successfully");
    }

}
