<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\HR\Database\Factories\LeaveRequestFactory;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'is_emergency',
        'attachments',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'is_emergency' => 'boolean',
        'attachments' => 'array',
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

    public function scopeByLeaveType($query, $leaveType)
    {
        return $query->where('leave_type', $leaveType);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('start_date', now()->year);
    }

    // Accessors
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute()
    {
        return $this->status === 'rejected';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getLeavePeriodAttribute()
    {
        return $this->start_date->format('M d') . ' - ' . $this->end_date->format('M d, Y');
    }

    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    // Methods
    public function approve($approvedBy = null, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    public function reject($approvedBy = null, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function isOverlapping($startDate, $endDate, $excludeId = null)
    {
        $query = $this->where('employee_id', $this->employee_id)
                     ->where('status', 'approved')
                     ->where(function ($q) use ($startDate, $endDate) {
                         $q->whereBetween('start_date', [$startDate, $endDate])
                           ->orWhereBetween('end_date', [$startDate, $endDate])
                           ->orWhere(function ($q2) use ($startDate, $endDate) {
                               $q2->where('start_date', '<=', $startDate)
                                  ->where('end_date', '>=', $endDate);
                           });
                     });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function calculateTotalDays()
    {
        $this->total_days = $this->start_date->diffInDays($this->end_date) + 1;
        $this->save();
    }

    public function isActive()
    {
        $today = now()->toDateString();
        return $this->status === 'approved' && 
               $this->start_date <= $today && 
               $this->end_date >= $today;
    }

    public function isUpcoming()
    {
        return $this->status === 'approved' && $this->start_date > now()->toDateString();
    }

    public function isPast()
    {
        return $this->end_date < now()->toDateString();
    }

    protected static function newFactory(): LeaveRequestFactory
    {
        return LeaveRequestFactory::new();
    }
}
