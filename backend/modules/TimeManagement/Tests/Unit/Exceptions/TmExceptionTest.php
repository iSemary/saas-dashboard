<?php

namespace Modules\TimeManagement\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Modules\TimeManagement\Domain\Exceptions\InvalidTimesheetStatusTransition;
use Modules\TimeManagement\Domain\Exceptions\InvalidTimeEntryStatusTransition;
use Modules\TimeManagement\Domain\Exceptions\OverlappingTimeEntry;
use Modules\TimeManagement\Domain\Exceptions\CalendarConflictDetected;
use Modules\TimeManagement\Domain\Exceptions\TimerAlreadyRunning;

class TmExceptionTest extends TestCase
{
    // ── InvalidTimesheetStatusTransition ─────────────────────────

    public function test_invalid_timesheet_status_transition_message(): void
    {
        $exception = new InvalidTimesheetStatusTransition('approved', 'draft');
        $this->assertSame("Cannot transition timesheet status from 'approved' to 'draft'.", $exception->getMessage());
    }

    // ── InvalidTimeEntryStatusTransition ─────────────────────────

    public function test_invalid_time_entry_status_transition_message(): void
    {
        $exception = new InvalidTimeEntryStatusTransition('stopped', 'active');
        $this->assertSame("Cannot transition time entry status from 'stopped' to 'active'.", $exception->getMessage());
    }

    // ── OverlappingTimeEntry ─────────────────────────────────────

    public function test_overlapping_time_entry_message(): void
    {
        $exception = new OverlappingTimeEntry('2025-06-15', '09:00', '10:00');
        $this->assertSame('Time entry overlaps with existing entry on 2025-06-15 (09:00 - 10:00).', $exception->getMessage());
    }

    // ── CalendarConflictDetected ─────────────────────────────────

    public function test_calendar_conflict_detected_default_count(): void
    {
        $exception = new CalendarConflictDetected();
        $this->assertSame('Calendar conflict detected. 1 conflicting event(s) found.', $exception->getMessage());
    }

    public function test_calendar_conflict_detected_custom_count(): void
    {
        $exception = new CalendarConflictDetected(3);
        $this->assertSame('Calendar conflict detected. 3 conflicting event(s) found.', $exception->getMessage());
    }

    // ── TimerAlreadyRunning ──────────────────────────────────────

    public function test_timer_already_running_message(): void
    {
        $exception = new TimerAlreadyRunning();
        $this->assertSame('A timer is already running for this user. Stop it before starting a new one.', $exception->getMessage());
    }
}
