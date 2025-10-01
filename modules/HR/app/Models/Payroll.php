<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\HR\Database\Factories\PayrollFactory;

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
        'created_by',
        'approved_by',
        'approved_at',
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

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPayPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('pay_period_start', [$startDate, $endDate]);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'calculated']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('pay_date', now()->month)
                    ->whereYear('pay_date', now()->year);
    }

    // Accessors
    public function getIsDraftAttribute()
    {
        return $this->status === 'draft';
    }

    public function getIsCalculatedAttribute()
    {
        return $this->status === 'calculated';
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getPayPeriodAttribute()
    {
        return $this->pay_period_start->format('M d') . ' - ' . $this->pay_period_end->format('M d, Y');
    }

    // Methods
    public function calculate()
    {
        // Calculate gross pay
        $this->gross_pay = $this->basic_salary + $this->overtime_pay + $this->bonus + $this->allowances;
        
        // Calculate total deductions
        $this->total_deductions = $this->tax_deduction + $this->social_security + $this->health_insurance + $this->other_deductions;
        
        // Calculate net pay
        $this->net_pay = $this->gross_pay - $this->total_deductions;
        
        $this->status = 'calculated';
        $this->save();
    }

    public function approve($approvedBy = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function isOverdue()
    {
        return $this->pay_date < now()->toDateString() && $this->status !== 'paid';
    }

    public function getFormattedAmount($amount)
    {
        return number_format($amount, 2) . ' ' . $this->currency;
    }

    protected static function newFactory(): PayrollFactory
    {
        return PayrollFactory::new();
    }
}
