<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_leave_types';

    protected $fillable = [
        'name',
        'code',
        'color',
        'is_paid',
        'requires_approval',
        'max_consecutive_days',
        'min_notice_days',
        'allow_half_day',
        'allow_negative_balance',
        'is_active',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'requires_approval' => 'boolean',
        'allow_half_day' => 'boolean',
        'allow_negative_balance' => 'boolean',
        'is_active' => 'boolean',
        'custom_fields' => 'array',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
