<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\HR\Database\Factories\AttendanceFactory;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'break_start',
        'break_end',
        'total_hours',
        'break_duration',
        'overtime_hours',
        'status',
        'is_approved',
        'approved_by',
        'approved_at',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'total_hours' => 'decimal:2',
        'break_duration' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }

    // Accessors
    public function getIsPresentAttribute()
    {
        return $this->status === 'present';
    }

    public function getIsAbsentAttribute()
    {
        return $this->status === 'absent';
    }

    public function getIsLateAttribute()
    {
        return $this->status === 'late';
    }

    public function getIsHalfDayAttribute()
    {
        return $this->status === 'half_day';
    }

    public function getWorkingHoursAttribute()
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        $totalMinutes = $this->check_out->diffInMinutes($this->check_in);
        $breakMinutes = $this->break_duration * 60;
        
        return ($totalMinutes - $breakMinutes) / 60;
    }

    // Methods
    public function approve($approvedBy = null)
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function reject($approvedBy = null)
    {
        $this->update([
            'is_approved' => false,
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function calculateHours()
    {
        if (!$this->check_in || !$this->check_out) {
            return;
        }

        $totalMinutes = $this->check_out->diffInMinutes($this->check_in);
        $breakMinutes = $this->break_duration * 60;
        $workingMinutes = $totalMinutes - $breakMinutes;
        
        $this->total_hours = $workingMinutes / 60;
        
        // Calculate overtime (assuming 8 hours is standard)
        if ($this->total_hours > 8) {
            $this->overtime_hours = $this->total_hours - 8;
        } else {
            $this->overtime_hours = 0;
        }
        
        $this->save();
    }

    public function isOverdue()
    {
        return $this->date < now()->toDateString() && !$this->is_approved;
    }

    protected static function newFactory(): AttendanceFactory
    {
        return AttendanceFactory::new();
    }
}
