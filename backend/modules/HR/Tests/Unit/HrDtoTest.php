<?php

namespace Modules\HR\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\HR\Application\DTOs\CreateDepartmentData;
use Modules\HR\Application\DTOs\RequestLeaveData;

class HrDtoTest extends TestCase
{
    // ── CreateDepartmentData ──────────────────────────────────────

    public function test_create_department_data_construction(): void
    {
        $dto = new CreateDepartmentData(
            name: 'Engineering',
            code: 'ENG',
            parent_id: null,
            manager_id: 1,
            description: 'Engineering department',
            status: 'active',
            custom_fields: null,
        );

        $this->assertSame('Engineering', $dto->name);
        $this->assertSame('ENG', $dto->code);
        $this->assertNull($dto->parent_id);
        $this->assertSame(1, $dto->manager_id);
        $this->assertSame('active', $dto->status);
    }

    public function test_create_department_data_to_array(): void
    {
        $dto = new CreateDepartmentData(
            name: 'Marketing',
            code: 'MKT',
            parent_id: 2,
            manager_id: 3,
            description: 'Marketing team',
            status: 'active',
            custom_fields: ['location' => 'NYC'],
        );

        $arr = $dto->toArray();

        $this->assertSame('Marketing', $arr['name']);
        $this->assertSame('MKT', $arr['code']);
        $this->assertSame(2, $arr['parent_id']);
        $this->assertSame(3, $arr['manager_id']);
        $this->assertSame('active', $arr['status']);
        $this->assertSame(['location' => 'NYC'], $arr['custom_fields']);
    }

    // ── RequestLeaveData ──────────────────────────────────────────

    public function test_request_leave_data_construction(): void
    {
        $dto = new RequestLeaveData(
            employeeId: 1,
            leaveTypeId: 2,
            startDate: '2026-05-01',
            endDate: '2026-05-03',
            isHalfDay: false,
            halfDaySession: null,
            reason: 'Vacation',
        );

        $this->assertSame(1, $dto->employeeId);
        $this->assertSame(2, $dto->leaveTypeId);
        $this->assertSame('2026-05-01', $dto->startDate);
        $this->assertSame('2026-05-03', $dto->endDate);
        $this->assertFalse($dto->isHalfDay);
        $this->assertSame('Vacation', $dto->reason);
    }

    public function test_request_leave_data_half_day(): void
    {
        $dto = new RequestLeaveData(
            employeeId: 1,
            leaveTypeId: 2,
            startDate: '2026-05-01',
            endDate: '2026-05-01',
            isHalfDay: true,
            halfDaySession: 'first_half',
            reason: 'Appointment',
        );

        $this->assertTrue($dto->isHalfDay);
        $this->assertSame('first_half', $dto->halfDaySession);
    }

    public function test_request_leave_data_to_array(): void
    {
        $dto = new RequestLeaveData(
            employeeId: 5,
            leaveTypeId: 3,
            startDate: '2026-06-01',
            endDate: '2026-06-05',
            isHalfDay: false,
            halfDaySession: null,
            reason: 'Sick leave',
        );

        $arr = $dto->toArray();

        $this->assertSame(5, $arr['employee_id']);
        $this->assertSame(3, $arr['leave_type_id']);
        $this->assertSame('2026-06-01', $arr['start_date']);
        $this->assertSame('2026-06-05', $arr['end_date']);
        $this->assertFalse($arr['is_half_day']);
        $this->assertNull($arr['half_day_session']);
        $this->assertSame('Sick leave', $arr['reason']);
    }
}
