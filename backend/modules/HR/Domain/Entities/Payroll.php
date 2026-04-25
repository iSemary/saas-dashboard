<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payroll_number',
        'employee_id',
        'pay_period_start',
        'pay_period_end',
        'pay_date',
        'status',
        'basic_salary',
        'overtime_pay',
        'bonus',
        'allowances',
        'gross_pay',
        'tax_deduction',
        'social_security',
        'health_insurance',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'currency',
        'notes',
        'approved_by',
        'approved_at',
        'payslip_pdf_path',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'pay_date' => 'date',
        'basic_salary' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'allowances' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'social_security' => 'decimal:2',
        'health_insurance' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'approved_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }

    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'calculated']);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('pay_date', now()->month)
            ->whereYear('pay_date', now()->year);
    }

    public function calculate(): void
    {
        $this->gross_pay = $this->basic_salary + $this->overtime_pay + $this->bonus + $this->allowances;
        $this->total_deductions = $this->tax_deduction + $this->social_security + $this->health_insurance + $this->other_deductions;
        $this->net_pay = $this->gross_pay - $this->total_deductions;
        $this->status = 'calculated';
    }

    public function approve(int $approvedBy): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
