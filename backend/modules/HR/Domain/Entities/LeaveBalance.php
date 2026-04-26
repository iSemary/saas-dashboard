<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'hr_leave_balances';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'allocated',
        'accrued',
        'used',
        'carried_over',
        'remaining',
    ];

    protected $casts = [
        'allocated' => 'decimal:2',
        'accrued' => 'decimal:2',
        'used' => 'decimal:2',
        'carried_over' => 'decimal:2',
        'remaining' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByLeaveType($query, int $leaveTypeId)
    {
        return $query->where('leave_type_id', $leaveTypeId);
    }

    public function addAccrued(float $days): void
    {
        $this->accrued += $days;
        $this->recalculateRemaining();
    }

    public function useDays(float $days): void
    {
        $this->used += $days;
        $this->recalculateRemaining();
    }

    public function carryOver(float $days): void
    {
        $this->carried_over += $days;
        $this->recalculateRemaining();
    }

    public function recalculateRemaining(): void
    {
        $this->remaining = $this->allocated + $this->accrued + $this->carried_over - $this->used;
    }

    public function hasSufficientBalance(float $requestedDays): bool
    {
        return $this->remaining >= $requestedDays;
    }
}
