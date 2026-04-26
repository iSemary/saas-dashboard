<?php

namespace Modules\HR\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\HR\Domain\ValueObjects\EmploymentStatus;
use Modules\HR\Domain\ValueObjects\EmploymentType;
use Modules\HR\Domain\ValueObjects\LeaveStatus;
use Modules\HR\Domain\ValueObjects\AttendanceStatus;
use Modules\HR\Domain\ValueObjects\DepartmentStatus;
use Modules\HR\Domain\ValueObjects\Gender;
use Modules\HR\Domain\ValueObjects\MaritalStatus;
use Modules\HR\Domain\ValueObjects\PayFrequency;
use Modules\HR\Domain\ValueObjects\PositionLevel;
use Modules\HR\Domain\ValueObjects\LeaveSession;

class HrValueObjectTest extends TestCase
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

    public function test_employment_status_all_cases_covered(): void
    {
        $this->assertCount(6, EmploymentStatus::cases());
    }

    public function test_employment_status_label(): void
    {
        $this->assertSame('Active', EmploymentStatus::ACTIVE->label());
        $this->assertSame('On Leave', EmploymentStatus::ON_LEAVE->label());
        $this->assertSame('Probation', EmploymentStatus::PROBATION->label());
    }

    public function test_employment_status_is_active(): void
    {
        $this->assertTrue(EmploymentStatus::ACTIVE->isActive());
        $this->assertTrue(EmploymentStatus::PROBATION->isActive());
        $this->assertFalse(EmploymentStatus::TERMINATED->isActive());
    }

    public function test_employment_status_is_terminated(): void
    {
        $this->assertTrue(EmploymentStatus::TERMINATED->isTerminated());
        $this->assertFalse(EmploymentStatus::ACTIVE->isTerminated());
    }

    public function test_employment_status_valid_transitions(): void
    {
        $this->assertTrue(EmploymentStatus::canTransitionFrom('active', EmploymentStatus::ON_LEAVE));
        $this->assertTrue(EmploymentStatus::canTransitionFrom('active', EmploymentStatus::TERMINATED));
        $this->assertTrue(EmploymentStatus::canTransitionFrom('probation', EmploymentStatus::ACTIVE));
        $this->assertTrue(EmploymentStatus::canTransitionFrom('on_leave', EmploymentStatus::ACTIVE));
        $this->assertTrue(EmploymentStatus::canTransitionFrom('suspended', EmploymentStatus::ACTIVE));
    }

    public function test_employment_status_invalid_transitions(): void
    {
        $this->assertFalse(EmploymentStatus::canTransitionFrom('terminated', EmploymentStatus::ACTIVE));
        $this->assertFalse(EmploymentStatus::canTransitionFrom('active', EmploymentStatus::PROBATION));
    }

    // ── EmploymentType ────────────────────────────────────────────

    public function test_employment_type_values(): void
    {
        $this->assertSame('full_time', EmploymentType::FULL_TIME->value);
        $this->assertSame('part_time', EmploymentType::PART_TIME->value);
        $this->assertSame('contract', EmploymentType::CONTRACT->value);
        $this->assertSame('intern', EmploymentType::INTERN->value);
        $this->assertSame('freelance', EmploymentType::FREELANCE->value);
        $this->assertSame('consultant', EmploymentType::CONSULTANT->value);
    }

    public function test_employment_type_all_cases_covered(): void
    {
        $this->assertCount(6, EmploymentType::cases());
    }

    public function test_employment_type_label(): void
    {
        $this->assertSame('Full Time', EmploymentType::FULL_TIME->label());
        $this->assertSame('Part Time', EmploymentType::PART_TIME->label());
    }

    // ── LeaveStatus ───────────────────────────────────────────────

    public function test_leave_status_values(): void
    {
        $this->assertSame('pending', LeaveStatus::PENDING->value);
        $this->assertSame('approved', LeaveStatus::APPROVED->value);
        $this->assertSame('rejected', LeaveStatus::REJECTED->value);
        $this->assertSame('cancelled', LeaveStatus::CANCELLED->value);
    }

    public function test_leave_status_valid_transitions(): void
    {
        $this->assertTrue(LeaveStatus::PENDING->canTransitionTo(LeaveStatus::APPROVED));
        $this->assertTrue(LeaveStatus::PENDING->canTransitionTo(LeaveStatus::REJECTED));
        $this->assertTrue(LeaveStatus::PENDING->canTransitionTo(LeaveStatus::CANCELLED));
        $this->assertTrue(LeaveStatus::APPROVED->canTransitionTo(LeaveStatus::CANCELLED));
    }

    public function test_leave_status_invalid_transitions(): void
    {
        $this->assertFalse(LeaveStatus::CANCELLED->canTransitionTo(LeaveStatus::APPROVED));
        $this->assertFalse(LeaveStatus::APPROVED->canTransitionTo(LeaveStatus::PENDING));
        $this->assertFalse(LeaveStatus::REJECTED->canTransitionTo(LeaveStatus::APPROVED));
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

    public function test_attendance_status_all_cases_covered(): void
    {
        $this->assertCount(7, AttendanceStatus::cases());
    }

    // ── DepartmentStatus ──────────────────────────────────────────

    public function test_department_status_values(): void
    {
        $this->assertSame('active', DepartmentStatus::ACTIVE->value);
        $this->assertSame('inactive', DepartmentStatus::INACTIVE->value);
    }

    public function test_department_status_valid_transitions(): void
    {
        $this->assertTrue(DepartmentStatus::canTransitionFrom('active', DepartmentStatus::INACTIVE));
        $this->assertTrue(DepartmentStatus::canTransitionFrom('inactive', DepartmentStatus::ACTIVE));
    }

    public function test_department_status_invalid_transitions(): void
    {
        $this->assertFalse(DepartmentStatus::canTransitionFrom('active', DepartmentStatus::ACTIVE));
    }

    // ── Gender ────────────────────────────────────────────────────

    public function test_gender_values(): void
    {
        $this->assertSame('male', Gender::MALE->value);
        $this->assertSame('female', Gender::FEMALE->value);
        $this->assertSame('other', Gender::OTHER->value);
        $this->assertSame('prefer_not_to_say', Gender::PREFER_NOT_TO_SAY->value);
    }

    public function test_gender_all_cases_covered(): void
    {
        $this->assertCount(4, Gender::cases());
    }

    // ── MaritalStatus ─────────────────────────────────────────────

    public function test_marital_status_values(): void
    {
        $this->assertSame('single', MaritalStatus::SINGLE->value);
        $this->assertSame('married', MaritalStatus::MARRIED->value);
        $this->assertSame('divorced', MaritalStatus::DIVORCED->value);
        $this->assertSame('widowed', MaritalStatus::WIDOWED->value);
        $this->assertSame('separated', MaritalStatus::SEPARATED->value);
        $this->assertSame('domestic_partnership', MaritalStatus::DOMESTIC_PARTNERSHIP->value);
    }

    public function test_marital_status_all_cases_covered(): void
    {
        $this->assertCount(6, MaritalStatus::cases());
    }

    // ── PayFrequency ──────────────────────────────────────────────

    public function test_pay_frequency_values(): void
    {
        $this->assertSame('weekly', PayFrequency::WEEKLY->value);
        $this->assertSame('biweekly', PayFrequency::BIWEEKLY->value);
        $this->assertSame('semi_monthly', PayFrequency::SEMI_MONTHLY->value);
        $this->assertSame('monthly', PayFrequency::MONTHLY->value);
        $this->assertSame('quarterly', PayFrequency::QUARTERLY->value);
        $this->assertSame('annually', PayFrequency::ANNUALLY->value);
    }

    public function test_pay_frequency_pays_per_year(): void
    {
        $this->assertSame(52, PayFrequency::WEEKLY->paysPerYear());
        $this->assertSame(26, PayFrequency::BIWEEKLY->paysPerYear());
        $this->assertSame(24, PayFrequency::SEMI_MONTHLY->paysPerYear());
        $this->assertSame(12, PayFrequency::MONTHLY->paysPerYear());
        $this->assertSame(4, PayFrequency::QUARTERLY->paysPerYear());
        $this->assertSame(1, PayFrequency::ANNUALLY->paysPerYear());
    }

    // ── PositionLevel ─────────────────────────────────────────────

    public function test_position_level_values(): void
    {
        $this->assertSame('executive', PositionLevel::EXECUTIVE->value);
        $this->assertSame('director', PositionLevel::DIRECTOR->value);
        $this->assertSame('manager', PositionLevel::MANAGER->value);
        $this->assertSame('senior', PositionLevel::SENIOR->value);
        $this->assertSame('mid', PositionLevel::MID->value);
        $this->assertSame('junior', PositionLevel::JUNIOR->value);
        $this->assertSame('intern', PositionLevel::INTERN->value);
        $this->assertSame('contractor', PositionLevel::CONTRACTOR->value);
    }

    public function test_position_level_order(): void
    {
        $this->assertSame(1, PositionLevel::EXECUTIVE->order());
        $this->assertSame(8, PositionLevel::CONTRACTOR->order());
        $this->assertTrue(PositionLevel::EXECUTIVE->order() < PositionLevel::JUNIOR->order());
    }

    // ── LeaveSession ──────────────────────────────────────────────

    public function test_leave_session_values(): void
    {
        $this->assertSame('first_half', LeaveSession::FIRST_HALF->value);
        $this->assertSame('second_half', LeaveSession::SECOND_HALF->value);
    }

    public function test_leave_session_all_cases_covered(): void
    {
        $this->assertCount(2, LeaveSession::cases());
    }
}
