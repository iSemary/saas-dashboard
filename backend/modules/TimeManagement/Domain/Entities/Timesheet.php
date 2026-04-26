<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\TimeManagement\Domain\Events\TimesheetSubmitted;
use Modules\TimeManagement\Domain\Events\TimesheetApproved;
use Modules\TimeManagement\Domain\Events\TimesheetRejected;
use Modules\TimeManagement\Domain\ValueObjects\TimesheetStatus;
use Modules\TimeManagement\Domain\Exceptions\InvalidTimesheetStatusTransition;
use Modules\Auth\Entities\User;

class Timesheet extends Model
{
    use SoftDeletes;

    protected $table = 'tm_timesheets';

    protected $fillable = [
        'tenant_id', 'user_id', 'period_start', 'period_end',
        'total_minutes', 'overtime_minutes', 'status', 'notes',
        'submitted_by', 'submitted_at', 'approved_by', 'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function status(): TimesheetStatus
    {
        return TimesheetStatus::fromString($this->status);
    }

    public function transitionStatus(TimesheetStatus $to, ?string $userId = null, ?string $reason = null): void
    {
        $from = $this->status();

        if (!TimesheetStatus::canTransitionFrom($from, $to)) {
            throw new InvalidTimesheetStatusTransition($from->value, $to->value);
        }

        $this->status = $to->value;

        if ($to === TimesheetStatus::Submitted) {
            $this->submitted_by = $userId;
            $this->submitted_at = now();
            $this->save();
            event(new TimesheetSubmitted($this->id, $this->user_id));
        } elseif ($to === TimesheetStatus::Approved) {
            $this->approved_by = $userId;
            $this->approved_at = now();
            $this->save();
            event(new TimesheetApproved($this->id, $this->user_id, $userId));
        } elseif ($to === TimesheetStatus::Rejected) {
            $this->approved_by = $userId;
            $this->rejection_reason = $reason;
            $this->save();
            event(new TimesheetRejected($this->id, $this->user_id, $userId, $reason));
        } else {
            $this->save();
        }
    }

    public function recalculateTotals(): void
    {
        $this->total_minutes = $this->timeEntries()->sum('duration_minutes');
        $this->save();
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
