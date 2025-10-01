<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\HR\Database\Factories\EmployeeFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_number',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'national_id',
        'passport_number',
        'address',
        'hire_date',
        'termination_date',
        'employment_status',
        'job_title',
        'department',
        'manager_id',
        'salary',
        'currency',
        'pay_frequency',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'salary' => 'decimal:2',
        'custom_fields' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByJobTitle($query, $jobTitle)
    {
        return $query->where('job_title', $jobTitle);
    }

    public function scopeByManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getIsActiveAttribute()
    {
        return $this->employment_status === 'active';
    }

    public function getYearsOfServiceAttribute()
    {
        if (!$this->hire_date) {
            return 0;
        }

        $endDate = $this->termination_date ?? now();
        return $this->hire_date->diffInYears($endDate);
    }

    // Methods
    public function terminate($terminationDate = null)
    {
        $this->update([
            'employment_status' => 'terminated',
            'termination_date' => $terminationDate ?? now(),
        ]);
    }

    public function reactivate()
    {
        $this->update([
            'employment_status' => 'active',
            'termination_date' => null,
        ]);
    }

    public function getCurrentPayroll()
    {
        return $this->payrolls()
            ->where('status', '!=', 'cancelled')
            ->latest('pay_period_start')
            ->first();
    }

    public function getTotalLeaveDays($year = null)
    {
        $year = $year ?? now()->year;
        
        return $this->leaveRequests()
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('total_days');
    }

    protected static function newFactory(): EmployeeFactory
    {
        return EmployeeFactory::new();
    }
}
