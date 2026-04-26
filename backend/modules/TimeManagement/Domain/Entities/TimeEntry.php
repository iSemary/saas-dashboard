<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\TimeManagement\Domain\Events\TimeEntryCreated;
use Modules\TimeManagement\Domain\ValueObjects\TimeEntryStatus;
use Modules\TimeManagement\Domain\Exceptions\InvalidTimeEntryStatusTransition;
use Modules\Auth\Entities\User;

class TimeEntry extends Model
{
    use SoftDeletes;

    protected $table = 'tm_time_entries';

    protected $fillable = [
        'tenant_id', 'user_id', 'project_id', 'task_id',
        'date', 'start_time', 'end_time', 'duration_minutes',
        'source', 'is_billable', 'description', 'status',
        'timesheet_id', 'time_session_id',
    ];

    protected $casts = [
        'date' => 'date',
        'duration_minutes' => 'integer',
        'is_billable' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class);
    }

    public function timeSession(): BelongsTo
    {
        return $this->belongsTo(TimeSession::class);
    }

    public function status(): TimeEntryStatus
    {
        return TimeEntryStatus::fromString($this->status);
    }

    public function transitionStatus(TimeEntryStatus $to): void
    {
        $from = $this->status();

        if (!TimeEntryStatus::canTransitionFrom($from, $to)) {
            throw new InvalidTimeEntryStatusTransition($from->value, $to->value);
        }

        $this->status = $to->value;
        $this->save();
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    public function scopeBillable($query)
    {
        return $query->where('is_billable', true);
    }

    protected static function booted(): void
    {
        static::created(function (TimeEntry $entry) {
            event(new TimeEntryCreated($entry->id, $entry->user_id, $entry->duration_minutes));
        });
    }
}
