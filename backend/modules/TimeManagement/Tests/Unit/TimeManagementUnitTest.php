<?php

namespace Modules\TimeManagement\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\TimeManagement\Application\DTOs\CreateTimeEntryData;
use Modules\TimeManagement\Application\DTOs\CreateTimesheetData;
use Modules\TimeManagement\Application\DTOs\CreateCalendarEventData;
use Modules\TimeManagement\Domain\ValueObjects\TimesheetStatus;
use Modules\TimeManagement\Domain\ValueObjects\TimeEntrySource;

class TimeManagementUnitTest extends TestCase
{
    // --- CreateTimeEntryData ---

    public function test_create_time_entry_data_can_be_instantiated(): void
    {
        $dto = new CreateTimeEntryData(
            tenantId: 'tenant-1',
            userId: 'user-1',
            date: '2025-06-15',
            durationMinutes: 120,
            projectId: 'proj-1',
            taskId: 'task-1',
            source: TimeEntrySource::Manual->value,
            isBillable: true,
            description: 'Worked on feature',
        );

        $this->assertEquals('user-1', $dto->userId);
        $this->assertEquals(120, $dto->durationMinutes);
        $this->assertTrue($dto->isBillable);
    }

    public function test_create_time_entry_data_to_array_filters_nulls(): void
    {
        $dto = new CreateTimeEntryData(
            tenantId: 'tenant-1',
            userId: 'user-1',
            date: '2025-06-15',
            durationMinutes: 60,
        );

        $array = $dto->toArray();
        $this->assertArrayHasKey('user_id', $array);
        $this->assertArrayNotHasKey('project_id', $array);
        $this->assertArrayNotHasKey('task_id', $array);
    }

    // --- CreateTimesheetData ---

    public function test_create_timesheet_data_can_be_instantiated(): void
    {
        $dto = new CreateTimesheetData(
            tenantId: 'tenant-1',
            userId: 'user-1',
            periodStart: '2025-06-09',
            periodEnd: '2025-06-15',
        );

        $this->assertEquals('user-1', $dto->userId);
        $this->assertEquals('2025-06-09', $dto->periodStart);
    }

    public function test_create_timesheet_data_to_array(): void
    {
        $dto = new CreateTimesheetData(
            tenantId: 'tenant-1',
            userId: 'user-1',
            periodStart: '2025-06-09',
            periodEnd: '2025-06-15',
        );

        $array = $dto->toArray();
        $this->assertCount(4, $array);
        $this->assertArrayHasKey('tenant_id', $array);
        $this->assertArrayHasKey('period_start', $array);
    }

    // --- CreateCalendarEventData ---

    public function test_create_calendar_event_data_can_be_instantiated(): void
    {
        $dto = new CreateCalendarEventData(
            tenantId: 'tenant-1',
            userId: 'user-1',
            title: 'Team Standup',
            startsAt: '2025-06-15T09:00:00Z',
            endsAt: '2025-06-15T09:30:00Z',
            description: 'Daily standup',
            location: 'Room 101',
            attendees: ['user-2', 'user-3'],
        );

        $this->assertEquals('Team Standup', $dto->title);
        $this->assertCount(2, $dto->attendees);
    }

    public function test_create_calendar_event_data_to_array_filters_nulls(): void
    {
        $dto = new CreateCalendarEventData(
            tenantId: 'tenant-1',
            userId: 'user-1',
            title: 'Quick Sync',
            startsAt: '2025-06-15T10:00:00Z',
            endsAt: '2025-06-15T10:15:00Z',
        );

        $array = $dto->toArray();
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayNotHasKey('location', $array);
        $this->assertArrayNotHasKey('attendees', $array);
    }

    // --- TimesheetStatus ---

    public function test_timesheet_status_values(): void
    {
        $this->assertEquals('draft', TimesheetStatus::Draft->value);
        $this->assertEquals('submitted', TimesheetStatus::Submitted->value);
        $this->assertEquals('approved', TimesheetStatus::Approved->value);
        $this->assertEquals('rejected', TimesheetStatus::Rejected->value);
    }

    public function test_timesheet_status_valid_transitions(): void
    {
        $this->assertTrue(TimesheetStatus::canTransitionFrom(TimesheetStatus::Draft, TimesheetStatus::Submitted));
        $this->assertTrue(TimesheetStatus::canTransitionFrom(TimesheetStatus::Submitted, TimesheetStatus::Approved));
        $this->assertTrue(TimesheetStatus::canTransitionFrom(TimesheetStatus::Submitted, TimesheetStatus::Rejected));
        $this->assertTrue(TimesheetStatus::canTransitionFrom(TimesheetStatus::Rejected, TimesheetStatus::Draft));
    }

    public function test_timesheet_status_invalid_transitions(): void
    {
        $this->assertFalse(TimesheetStatus::canTransitionFrom(TimesheetStatus::Approved, TimesheetStatus::Draft));
        $this->assertFalse(TimesheetStatus::canTransitionFrom(TimesheetStatus::Draft, TimesheetStatus::Approved));
    }

    // --- TimeEntrySource ---

    public function test_time_entry_source_values(): void
    {
        $this->assertEquals('manual', TimeEntrySource::Manual->value);
        $this->assertEquals('timer', TimeEntrySource::Timer->value);
        $this->assertEquals('calendar', TimeEntrySource::Calendar->value);
    }
}
