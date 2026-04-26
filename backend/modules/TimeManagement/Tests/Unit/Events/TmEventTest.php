<?php

namespace Modules\TimeManagement\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Modules\TimeManagement\Domain\Events\TimeEntryCreated;
use Modules\TimeManagement\Domain\Events\TimerStarted;
use Modules\TimeManagement\Domain\Events\TimerStopped;
use Modules\TimeManagement\Domain\Events\TimesheetSubmitted;
use Modules\TimeManagement\Domain\Events\TimesheetApproved;
use Modules\TimeManagement\Domain\Events\TimesheetRejected;
use Modules\TimeManagement\Domain\Events\CalendarEventCreated;
use Modules\TimeManagement\Domain\Events\CalendarEventUpdated;
use Modules\TimeManagement\Domain\Events\CalendarEventDeleted;
use Modules\TimeManagement\Domain\Events\AnomalyDetected;

class TmEventTest extends TestCase
{
    // ── TimeEntryCreated ─────────────────────────────────────────

    public function test_time_entry_created_stores_ids_and_duration(): void
    {
        $event = new TimeEntryCreated('entry-1', 'user-1', 120);

        $this->assertSame('entry-1', $event->entryId);
        $this->assertSame('user-1', $event->userId);
        $this->assertSame(120, $event->durationMinutes);
    }

    // ── TimerStarted ─────────────────────────────────────────────

    public function test_timer_started_stores_session(): void
    {
        $event = new TimerStarted('session-1', 'user-1', 'proj-1', 'task-1');

        $this->assertSame('session-1', $event->sessionId);
        $this->assertSame('user-1', $event->userId);
        $this->assertSame('proj-1', $event->projectId);
        $this->assertSame('task-1', $event->taskId);
    }

    public function test_timer_started_project_and_task_optional(): void
    {
        $event = new TimerStarted('session-1', 'user-1');

        $this->assertNull($event->projectId);
        $this->assertNull($event->taskId);
    }

    // ── TimerStopped ─────────────────────────────────────────────

    public function test_timer_stopped_stores_duration(): void
    {
        $event = new TimerStopped('session-1', 'user-1', 3600);

        $this->assertSame('session-1', $event->sessionId);
        $this->assertSame('user-1', $event->userId);
        $this->assertSame(3600, $event->durationSeconds);
    }

    // ── TimesheetSubmitted ───────────────────────────────────────

    public function test_timesheet_submitted_stores_ids(): void
    {
        $event = new TimesheetSubmitted('ts-1', 'user-1');

        $this->assertSame('ts-1', $event->timesheetId);
        $this->assertSame('user-1', $event->userId);
    }

    // ── TimesheetApproved ────────────────────────────────────────

    public function test_timesheet_approved_stores_approver(): void
    {
        $event = new TimesheetApproved('ts-1', 'user-1', 'manager-1');

        $this->assertSame('ts-1', $event->timesheetId);
        $this->assertSame('user-1', $event->userId);
        $this->assertSame('manager-1', $event->approvedBy);
    }

    // ── TimesheetRejected ────────────────────────────────────────

    public function test_timesheet_rejected_stores_rejector_and_reason(): void
    {
        $event = new TimesheetRejected('ts-1', 'user-1', 'manager-1', 'Missing entries');

        $this->assertSame('ts-1', $event->timesheetId);
        $this->assertSame('user-1', $event->userId);
        $this->assertSame('manager-1', $event->rejectedBy);
        $this->assertSame('Missing entries', $event->reason);
    }

    public function test_timesheet_rejected_reason_is_optional(): void
    {
        $event = new TimesheetRejected('ts-1', 'user-1', 'manager-1');

        $this->assertNull($event->reason);
    }

    // ── CalendarEventCreated ─────────────────────────────────────

    public function test_calendar_event_created_stores_ids(): void
    {
        $event = new CalendarEventCreated('evt-1', 'user-1');

        $this->assertSame('evt-1', $event->eventId);
        $this->assertSame('user-1', $event->userId);
    }

    // ── CalendarEventUpdated ─────────────────────────────────────

    public function test_calendar_event_updated_stores_ids(): void
    {
        $event = new CalendarEventUpdated('evt-1', 'user-1');

        $this->assertSame('evt-1', $event->eventId);
        $this->assertSame('user-1', $event->userId);
    }

    // ── CalendarEventDeleted ─────────────────────────────────────

    public function test_calendar_event_deleted_stores_ids(): void
    {
        $event = new CalendarEventDeleted('evt-1', 'user-1');

        $this->assertSame('evt-1', $event->eventId);
        $this->assertSame('user-1', $event->userId);
    }

    // ── AnomalyDetected ──────────────────────────────────────────

    public function test_anomaly_detected_stores_type_and_details(): void
    {
        $event = new AnomalyDetected('user-1', 'excessive_overtime', ['hours' => 60]);

        $this->assertSame('user-1', $event->userId);
        $this->assertSame('excessive_overtime', $event->anomalyType);
        $this->assertSame(['hours' => 60], $event->details);
    }

    public function test_anomaly_detected_details_default_empty(): void
    {
        $event = new AnomalyDetected('user-1', 'missing_clock_out');

        $this->assertSame([], $event->details);
    }
}
