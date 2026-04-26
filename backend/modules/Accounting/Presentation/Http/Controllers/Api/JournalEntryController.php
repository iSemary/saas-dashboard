<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Accounting\Presentation\Http\Requests\StoreJournalEntryRequest;
use Modules\Accounting\Presentation\Http\Requests\UpdateJournalEntryRequest;
use Illuminate\Routing\Controller;
use Modules\Accounting\Infrastructure\Persistence\JournalEntryRepositoryInterface;

class JournalEntryController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected JournalEntryRepositoryInterface $repository,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(StoreJournalEntryRequest $request): JsonResponse
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

    public function update(UpdateJournalEntryRequest $request, int $id): JsonResponse
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
        return $this->apiSuccess(['deleted' => $count], "${count} JournalEntry(s) deleted successfully");
    }

    public function post(int $id): JsonResponse
    {
        $entry = $this->repository->findOrFail($id);
        $entry->transitionState(\Modules\Accounting\Domain\ValueObjects\JournalEntryState::POSTED);
        return $this->apiSuccess($entry->fresh(), "Journal entry posted successfully");
    }

    public function cancel(int $id): JsonResponse
    {
        $entry = $this->repository->findOrFail($id);
        $entry->transitionState(\Modules\Accounting\Domain\ValueObjects\JournalEntryState::CANCELLED);
        return $this->apiSuccess($entry->fresh(), "Journal entry cancelled successfully");
    }

}
