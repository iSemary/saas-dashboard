<?php

namespace Modules\TimeManagement\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\TimeManagement\Domain\ValueObjects\TimesheetStatus;
use Modules\TimeManagement\Domain\ValueObjects\TimeEntryStatus;
use Modules\TimeManagement\Domain\ValueObjects\TimeEntrySource;
use Modules\TimeManagement\Domain\ValueObjects\AttendanceStatus;
use Modules\TimeManagement\Domain\ValueObjects\OvertimeRequestStatus;

class TimeManagementValueObjectTest extends TestCase
{
    // ── TimesheetStatus ───────────────────────────────────────────

    public function test_timesheet_status_values(): void
    {
        $this->assertSame('draft', TimesheetStatus::Draft->value);
        $this->assertSame('submitted', TimesheetStatus::Submitted->value);
        $this->assertSame('approved', TimesheetStatus::Approved->value);
        $this->assertSame('rejected', TimesheetStatus::Rejected->value);
    }

    public function test_timesheet_status_all_cases_covered(): void
    {
        $this->assertCount(4, TimesheetStatus::cases());
    }

    public function test_timesheet_status_label(): void
    {
        $this->assertSame('Draft', TimesheetStatus::Draft->label());
        $this->assertSame('Submitted', TimesheetStatus::Submitted->label());
        $this->assertSame('Approved', TimesheetStatus::Approved->label());
        $this->assertSame('Rejected', TimesheetStatus::Rejected->label());
    }

    public function test_timesheet_status_from_string(): void
    {
        $this->assertSame(TimesheetStatus::Approved, TimesheetStatus::fromString('approved'));
        $this->assertSame(TimesheetStatus::Draft, TimesheetStatus::fromString('draft'));
    }

    public function test_timesheet_status_valid_transitions(): void
    {
        $this->assertTrue(TimesheetStatus::canTransitionFrom(TimesheetStatus::Draft, TimesheetStatus::Submitted));
        $this->assertTrue(TimesheetStatus::canTransitionFrom(TimesheetStatus::Submitted, TimesheetStatus::Approved));
        $this->assertTrue(TimesheetStatus::canTransitionFrom(TimesheetStatus::Submitted, TimesheetStatus::Rejected));
        $this->assertTrue(TimesheetStatus::canTransitionFrom(TimesheetStatus::Rejected, TimesheetStatus::Draft));
        $this->assertTrue(TimesheetStatus::canTransitionFrom(TimesheetStatus::Rejected, TimesheetStatus::Submitted));
    }

    public function test_timesheet_status_invalid_transitions(): void
    {
        $this->assertFalse(TimesheetStatus::canTransitionFrom(TimesheetStatus::Approved, TimesheetStatus::Draft));
        $this->assertFalse(TimesheetStatus::canTransitionFrom(TimesheetStatus::Draft, TimesheetStatus::Approved));
        $this->assertFalse(TimesheetStatus::canTransitionFrom(TimesheetStatus::Approved, TimesheetStatus::Rejected));
    }

    // ── TimeEntryStatus ───────────────────────────────────────────

    public function test_time_entry_status_values(): void
    {
        $this->assertSame('draft', TimeEntryStatus::Draft->value);
        $this->assertSame('submitted', TimeEntryStatus::Submitted->value);
        $this->assertSame('approved', TimeEntryStatus::Approved->value);
        $this->assertSame('rejected', TimeEntryStatus::Rejected->value);
    }

    public function test_time_entry_status_all_cases_covered(): void
    {
        $this->assertCount(4, TimeEntryStatus::cases());
    }

    public function test_time_entry_status_valid_transitions(): void
    {
        $this->assertTrue(TimeEntryStatus::canTransitionFrom(TimeEntryStatus::Draft, TimeEntryStatus::Submitted));
        $this->assertTrue(TimeEntryStatus::canTransitionFrom(TimeEntryStatus::Submitted, TimeEntryStatus::Approved));
        $this->assertTrue(TimeEntryStatus::canTransitionFrom(TimeEntryStatus::Submitted, TimeEntryStatus::Rejected));
        $this->assertTrue(TimeEntryStatus::canTransitionFrom(TimeEntryStatus::Rejected, TimeEntryStatus::Draft));
    }

    public function test_time_entry_status_invalid_transitions(): void
    {
        $this->assertFalse(TimeEntryStatus::canTransitionFrom(TimeEntryStatus::Approved, TimeEntryStatus::Draft));
        $this->assertFalse(TimeEntryStatus::canTransitionFrom(TimeEntryStatus::Draft, TimeEntryStatus::Approved));
    }

    // ── TimeEntrySource ───────────────────────────────────────────

    public function test_time_entry_source_values(): void
    {
        $this->assertSame('manual', TimeEntrySource::Manual->value);
        $this->assertSame('timer', TimeEntrySource::Timer->value);
        $this->assertSame('calendar', TimeEntrySource::Calendar->value);
    }

    public function test_time_entry_source_all_cases_covered(): void
    {
        $this->assertCount(3, TimeEntrySource::cases());
    }

    public function test_time_entry_source_label(): void
    {
        $this->assertSame('Manual', TimeEntrySource::Manual->label());
        $this->assertSame('Timer', TimeEntrySource::Timer->label());
        $this->assertSame('Calendar', TimeEntrySource::Calendar->label());
    }

    public function test_time_entry_source_from_string(): void
    {
        $this->assertSame(TimeEntrySource::Timer, TimeEntrySource::fromString('timer'));
    }

    // ── AttendanceStatus ──────────────────────────────────────────

    public function test_attendance_status_values(): void
    {
        $this->assertSame('present', AttendanceStatus::Present->value);
        $this->assertSame('absent', AttendanceStatus::Absent->value);
        $this->assertSame('late', AttendanceStatus::Late->value);
        $this->assertSame('half_day', AttendanceStatus::HalfDay->value);
        $this->assertSame('holiday', AttendanceStatus::Holiday->value);
        $this->assertSame('leave', AttendanceStatus::Leave->value);
    }

    public function test_attendance_status_all_cases_covered(): void
    {
        $this->assertCount(6, AttendanceStatus::cases());
    }

    public function test_attendance_status_label(): void
    {
        $this->assertSame('Present', AttendanceStatus::Present->label());
        $this->assertSame('Absent', AttendanceStatus::Absent->label());
        $this->assertSame('Late', AttendanceStatus::Late->label());
        $this->assertSame('Half Day', AttendanceStatus::HalfDay->label());
    }

    public function test_attendance_status_from_string(): void
    {
        $this->assertSame(AttendanceStatus::Present, AttendanceStatus::fromString('present'));
    }

    // ── OvertimeRequestStatus ─────────────────────────────────────

    public function test_overtime_request_status_values(): void
    {
        $this->assertSame('pending', OvertimeRequestStatus::Pending->value);
        $this->assertSame('approved', OvertimeRequestStatus::Approved->value);
        $this->assertSame('rejected', OvertimeRequestStatus::Rejected->value);
    }

    public function test_overtime_request_status_all_cases_covered(): void
    {
        $this->assertCount(3, OvertimeRequestStatus::cases());
    }

    public function test_overtime_request_status_valid_transitions(): void
    {
        $this->assertTrue(OvertimeRequestStatus::canTransitionFrom(OvertimeRequestStatus::Pending, OvertimeRequestStatus::Approved));
        $this->assertTrue(OvertimeRequestStatus::canTransitionFrom(OvertimeRequestStatus::Pending, OvertimeRequestStatus::Rejected));
    }

    public function test_overtime_request_status_invalid_transitions(): void
    {
        $this->assertFalse(OvertimeRequestStatus::canTransitionFrom(OvertimeRequestStatus::Approved, OvertimeRequestStatus::Pending));
        $this->assertFalse(OvertimeRequestStatus::canTransitionFrom(OvertimeRequestStatus::Rejected, OvertimeRequestStatus::Pending));
        $this->assertFalse(OvertimeRequestStatus::canTransitionFrom(OvertimeRequestStatus::Approved, OvertimeRequestStatus::Rejected));
    }
}
