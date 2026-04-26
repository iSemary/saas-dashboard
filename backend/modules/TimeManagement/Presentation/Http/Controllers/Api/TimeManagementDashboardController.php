<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TimeManagement\Infrastructure\Persistence\TimeSessionRepositoryInterface;
use Modules\TimeManagement\Infrastructure\Persistence\TimeEntryRepositoryInterface;
use Modules\TimeManagement\Infrastructure\Persistence\TimesheetRepositoryInterface;
use Modules\TimeManagement\Infrastructure\Persistence\CalendarEventRepositoryInterface;

class TimeManagementDashboardController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected TimeSessionRepositoryInterface $sessionRepository,
        protected TimeEntryRepositoryInterface $entryRepository,
        protected TimesheetRepositoryInterface $timesheetRepository,
        protected CalendarEventRepositoryInterface $eventRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $activeTimer = $this->sessionRepository->findActiveByUser($userId);
        $todayEntries = $this->entryRepository->sumDurationByUserAndDate($userId, now()->toDateString());
        $pendingTimesheets = $this->timesheetRepository->count(['user_id' => $userId, 'status' => 'submitted']);
        $upcomingEvents = $this->eventRepository->list(['user_id' => $userId, 'upcoming' => true, 'limit' => 5]);

        return $this->apiSuccess([
            'active_timer' => $activeTimer,
            'today_minutes' => $todayEntries,
            'pending_timesheets' => $pendingTimesheets,
            'upcoming_events' => $upcomingEvents,
        ]);
    }
}
