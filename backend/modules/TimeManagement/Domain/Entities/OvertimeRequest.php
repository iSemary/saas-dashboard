<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\TimeManagement\Domain\ValueObjects\OvertimeRequestStatus;
use Modules\TimeManagement\Domain\Exceptions\InvalidTimeEntryStatusTransition;
use Modules\Auth\Entities\User;

class OvertimeRequest extends Model
{
    protected $table = 'tm_overtime_requests';

    protected $fillable = [
        'tenant_id', 'user_id', 'date', 'requested_minutes',
        'approved_minutes', 'reason', 'status',
        'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'requested_minutes' => 'integer',
        'approved_minutes' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function status(): OvertimeRequestStatus
    {
        return OvertimeRequestStatus::fromString($this->status);
    }

    public function approve(string $approvedBy, int $approvedMinutes): void
    {
        $from = $this->status();
        if (!OvertimeRequestStatus::canTransitionFrom($from, OvertimeRequestStatus::Approved)) {
            throw new InvalidTimeEntryStatusTransition($from->value, OvertimeRequestStatus::Approved->value);
        }

        $this->status = OvertimeRequestStatus::Approved->value;
        $this->approved_by = $approvedBy;
        $this->approved_minutes = $approvedMinutes;
        $this->approved_at = now();
        $this->save();
    }

    public function reject(string $rejectedBy, string $reason): void
    {
        $from = $this->status();
        if (!OvertimeRequestStatus::canTransitionFrom($from, OvertimeRequestStatus::Rejected)) {
            throw new InvalidTimeEntryStatusTransition($from->value, OvertimeRequestStatus::Rejected->value);
        }

        $this->status = OvertimeRequestStatus::Rejected->value;
        $this->approved_by = $rejectedBy;
        $this->rejection_reason = $reason;
        $this->save();
    }

    public function scopePending($query)
    {
        return $query->where('status', OvertimeRequestStatus::Pending->value);
    }
}
