<?php

namespace Modules\HR\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\HR\Domain\ValueObjects\EmploymentStatus;
use Modules\HR\Domain\ValueObjects\DepartmentStatus;
use Modules\HR\Domain\ValueObjects\LeaveStatus;
use Modules\HR\Domain\ValueObjects\AttendanceStatus;
use Modules\HR\Domain\Exceptions\HrDomainException;
use Modules\HR\Domain\Exceptions\InvalidEmploymentStatusTransition;
use Modules\HR\Domain\Exceptions\InvalidDepartmentStatusTransition;
use Modules\HR\Domain\Exceptions\DepartmentHasEmployees;
use Modules\HR\Domain\Exceptions\DepartmentHasSubDepartments;
use Modules\HR\Domain\Exceptions\CircularDepartmentHierarchy;

class HrVoExceptionTest extends TestCase
{
    // ── EmploymentStatus ──────────────────────────────────────────

    public function test_employment_status_values(): void
    {
        $this->assertSame('active', EmploymentStatus::ACTIVE->value);
        $this->assertSame('inactive', EmploymentStatus::INACTIVE->value);
        $this->assertSame('terminated', EmploymentStatus::TERMINATED->value);
        $this->assertSame('on_leave', EmploymentStatus::ON_LEAVE->value);
        $this->assertSame('probation', EmploymentStatus::PROBATION->value);
        $this->assertSame('suspended', EmploymentStatus::SUSPENDED->value);
    }

    public function test_employment_status_all_cases(): void
    {
        $this->assertCount(6, EmploymentStatus::cases());
    }

    public function test_employment_status_label(): void
    {
        $this->assertSame('Active', EmploymentStatus::ACTIVE->label());
        $this->assertSame('On Leave', EmploymentStatus::ON_LEAVE->label());
        $this->assertSame('Probation', EmploymentStatus::PROBATION->label());
    }

    public function test_employment_status_color(): void
    {
        $this->assertSame('green', EmploymentStatus::ACTIVE->color());
        $this->assertSame('red', EmploymentStatus::TERMINATED->color());
        $this->assertSame('yellow', EmploymentStatus::PROBATION->color());
    }

    public function test_employment_status_is_active(): void
    {
        $this->assertTrue(EmploymentStatus::ACTIVE->isActive());
        $this->assertTrue(EmploymentStatus::PROBATION->isActive());
        $this->assertFalse(EmploymentStatus::TERMINATED->isActive());
        $this->assertFalse(EmploymentStatus::SUSPENDED->isActive());
    }

    public function test_employment_status_is_terminated(): void
    {
        $this->assertTrue(EmploymentStatus::TERMINATED->isTerminated());
        $this->assertFalse(EmploymentStatus::ACTIVE->isTerminated());
    }

    public function test_employment_status_can_transition_from_active(): void
    {
        $this->assertTrue(EmploymentStatus::canTransitionFrom('active', EmploymentStatus::INACTIVE));
        $this->assertTrue(EmploymentStatus::canTransitionFrom('active', EmploymentStatus::TERMINATED));
        $this->assertFalse(EmploymentStatus::canTransitionFrom('active', EmploymentStatus::ACTIVE));
    }

    public function test_employment_status_can_transition_from_terminated(): void
    {
        $this->assertFalse(EmploymentStatus::canTransitionFrom('terminated', EmploymentStatus::ACTIVE));
    }

    // ── DepartmentStatus ──────────────────────────────────────────

    public function test_department_status_values(): void
    {
        $this->assertSame('active', DepartmentStatus::ACTIVE->value);
        $this->assertSame('inactive', DepartmentStatus::INACTIVE->value);
    }

    public function test_department_status_label(): void
    {
        $this->assertSame('Active', DepartmentStatus::ACTIVE->label());
        $this->assertSame('Inactive', DepartmentStatus::INACTIVE->label());
    }

    public function test_department_status_can_transition(): void
    {
        $this->assertTrue(DepartmentStatus::canTransitionFrom('active', DepartmentStatus::INACTIVE));
        $this->assertTrue(DepartmentStatus::canTransitionFrom('inactive', DepartmentStatus::ACTIVE));
        $this->assertFalse(DepartmentStatus::canTransitionFrom('active', DepartmentStatus::ACTIVE));
    }

    // ── LeaveStatus ───────────────────────────────────────────────

    public function test_leave_status_values(): void
    {
        $this->assertSame('pending', LeaveStatus::PENDING->value);
        $this->assertSame('approved', LeaveStatus::APPROVED->value);
        $this->assertSame('rejected', LeaveStatus::REJECTED->value);
        $this->assertSame('cancelled', LeaveStatus::CANCELLED->value);
    }

    public function test_leave_status_can_transition_from_pending(): void
    {
        $this->assertTrue(LeaveStatus::PENDING->canTransitionTo(LeaveStatus::APPROVED));
        $this->assertTrue(LeaveStatus::PENDING->canTransitionTo(LeaveStatus::REJECTED));
        $this->assertTrue(LeaveStatus::PENDING->canTransitionTo(LeaveStatus::CANCELLED));
        $this->assertFalse(LeaveStatus::PENDING->canTransitionTo(LeaveStatus::PENDING));
    }

    public function test_leave_status_can_transition_from_cancelled(): void
    {
        $this->assertFalse(LeaveStatus::CANCELLED->canTransitionTo(LeaveStatus::PENDING));
        $this->assertFalse(LeaveStatus::CANCELLED->canTransitionTo(LeaveStatus::APPROVED));
    }

    // ── AttendanceStatus ──────────────────────────────────────────

    public function test_attendance_status_values(): void
    {
        $this->assertSame('present', AttendanceStatus::PRESENT->value);
        $this->assertSame('absent', AttendanceStatus::ABSENT->value);
        $this->assertSame('late', AttendanceStatus::LATE->value);
        $this->assertSame('half_day', AttendanceStatus::HALF_DAY->value);
        $this->assertSame('leave', AttendanceStatus::LEAVE->value);
        $this->assertSame('holiday', AttendanceStatus::HOLIDAY->value);
        $this->assertSame('week_off', AttendanceStatus::WEEK_OFF->value);
    }

    public function test_attendance_status_all_cases(): void
    {
        $this->assertCount(7, AttendanceStatus::cases());
    }

    public function test_attendance_status_labels(): void
    {
        $this->assertSame('Present', AttendanceStatus::PRESENT->label());
        $this->assertSame('Half Day', AttendanceStatus::HALF_DAY->label());
        $this->assertSame('Week Off', AttendanceStatus::WEEK_OFF->label());
    }

    // ── Exceptions ────────────────────────────────────────────────

    public function test_hr_domain_exception_extends_runtime(): void
    {
        $e = new HrDomainException('test');
        $this->assertInstanceOf(\RuntimeException::class, $e);
    }

    public function test_invalid_employment_status_transition_message(): void
    {
        $e = new InvalidEmploymentStatusTransition('terminated', 'active');
        $this->assertSame("Cannot transition employment status from 'terminated' to 'active'", $e->getMessage());
    }

    public function test_invalid_department_status_transition_message(): void
    {
        $e = new InvalidDepartmentStatusTransition('inactive', 'inactive');
        $this->assertSame("Cannot transition department status from 'inactive' to 'inactive'", $e->getMessage());
    }

    public function test_department_has_employees_message(): void
    {
        $e = new DepartmentHasEmployees(5, 10);
        $this->assertSame("Cannot delete department 5 - it has 10 employee(s). Reassign employees first.", $e->getMessage());
    }

    public function test_department_has_sub_departments_message(): void
    {
        $e = new DepartmentHasSubDepartments(5, 3);
        $this->assertSame("Cannot delete department 5 - it has 3 sub-department(s). Reassign sub-departments first.", $e->getMessage());
    }

    public function test_circular_department_hierarchy_message(): void
    {
        $e = new CircularDepartmentHierarchy(1, 5);
        $this->assertSame("Cannot set department 5 as parent of 1 - would create circular hierarchy", $e->getMessage());
    }
}
