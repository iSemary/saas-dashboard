<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\CalendarEventRepositoryInterface;
use Modules\TimeManagement\Application\DTOs\CreateCalendarEventData;
use Modules\TimeManagement\Application\UseCases\Calendar\CreateCalendarEvent;
use Modules\TimeManagement\Domain\Strategies\ConflictDetection\ConflictDetectionStrategyInterface;

class CalendarEventController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected CalendarEventRepositoryInterface $repository,
        protected CreateCalendarEvent $createCalendarEvent,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = array_merge($request->only(['user_id', 'starts_after', 'ends_before']), [
            'tenant_id' => $request->user()->tenant_id,
        ]);
        $result = $this->repository->paginate($filters, $request->get('per_page', 15));
        return $this->apiPaginated($result);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = CreateCalendarEventData::fromRequest($request);
            $skipConflict = $request->input('skip_conflict_check', false);
            $event = $this->createCalendarEvent->execute($data, $skipConflict);
            return $this->apiSuccess($event, translate('message.created_successfully'), 201);
        } catch (\Modules\TimeManagement\Domain\Exceptions\CalendarConflictDetected $e) {
            return $this->apiError($e->getMessage(), 409);
        }
    }

    public function show(string $id): JsonResponse
    {
        return $this->apiSuccess($this->repository->findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $event = $this->repository->update($id, $request->all());
        return $this->apiSuccess($event, translate('message.updated_successfully'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->repository->delete($id);
        return $this->apiSuccess(null, translate('message.deleted_successfully'));
    }

    public function generateMeetingLink(Request $request, string $id): JsonResponse
    {
        $event = $this->repository->findOrFail($id);
        $provider = $request->input('provider', 'google_meet');

        $strategy = app("tm.meeting-link.{$provider}");
        if ($strategy) {
            $link = $strategy->generateLink($event->title, $event->starts_at, $event->ends_at, $event->attendees ?? []);
            $event->update(['meeting_link' => $link]);
            return $this->apiSuccess($event->fresh(), translate('message.action_completed'));
        }

        return $this->apiError(translate('message.operation_failed'), 400);
    }

    public function checkConflicts(Request $request): JsonResponse
    {
        $strategy = app(ConflictDetectionStrategyInterface::class);
        $conflicts = $strategy->detectConflicts(
            $request->user()->id,
            $request->input('starts_at'),
            $request->input('ends_at'),
        );
        return $this->apiSuccess(['conflict_count' => count($conflicts), 'conflicts' => $conflicts]);
    }
}
