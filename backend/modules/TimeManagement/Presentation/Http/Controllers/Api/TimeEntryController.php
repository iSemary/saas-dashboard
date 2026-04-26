<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use App\Http\Requests\TableListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\TimeEntryRepositoryInterface;
use Modules\TimeManagement\Application\DTOs\CreateTimeEntryData;
use Modules\TimeManagement\Application\UseCases\TimeEntry\CreateTimeEntry;
use Modules\TimeManagement\Application\UseCases\TimeEntry\ChangeTimeEntryStatus;
use Modules\TimeManagement\Domain\ValueObjects\TimeEntryStatus;

class TimeEntryController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected TimeEntryRepositoryInterface $repository,
        protected CreateTimeEntry $createTimeEntry,
        protected ChangeTimeEntryStatus $changeStatus,
    ) {}

    public function index(TableListRequest $request): JsonResponse
    {
        $params = $request->getTableParams();
        $result = $this->repository->getTableList($params);
        return $this->apiSuccess($result);
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateTimeEntryData::fromRequest($request);
        $entry = $this->createTimeEntry->execute($data);
        return $this->apiSuccess($entry, translate('message.created_successfully'), 201);
    }

    public function show(string $id): JsonResponse
    {
        return $this->apiSuccess($this->repository->findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $entry = $this->repository->update($id, $request->all());
        return $this->apiSuccess($entry, translate('message.updated_successfully'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function split(Request $request, string $id): JsonResponse
    {
        $entry = $this->repository->findOrFail($id);
        $splitMinutes = $request->input('split_minutes', $entry->duration_minutes / 2);

        $newEntry = $this->repository->create([
            'tenant_id' => $entry->tenant_id,
            'user_id' => $entry->user_id,
            'project_id' => $entry->project_id,
            'task_id' => $entry->task_id,
            'date' => $entry->date,
            'duration_minutes' => $entry->duration_minutes - $splitMinutes,
            'description' => $entry->description . ' (split)',
            'source' => $entry->source,
            'is_billable' => $entry->is_billable,
        ]);

        $entry->update(['duration_minutes' => $splitMinutes]);
        return $this->apiSuccess(['original' => $entry->fresh(), 'split' => $newEntry], translate('message.action_completed'));
    }
}
