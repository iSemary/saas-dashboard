<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\TimeManagement\Domain\Events\CalendarEventCreated;
use Modules\TimeManagement\Domain\Events\CalendarEventUpdated;
use Modules\TimeManagement\Domain\Events\CalendarEventDeleted;
use Modules\Auth\Entities\User;

class CalendarEvent extends Model
{
    use SoftDeletes;

    protected $table = 'tm_calendar_events';

    protected $fillable = [
        'tenant_id', 'user_id', 'title', 'description',
        'starts_at', 'ends_at', 'is_all_day', 'location',
        'meeting_link', 'meeting_provider', 'recurrence_rule',
        'source', 'external_event_id', 'provider',
        'attendees', 'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_all_day' => 'boolean',
        'attendees' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSynced(): bool
    {
        return $this->source === 'synced' && $this->external_event_id !== null;
    }

    public function durationMinutes(): int
    {
        if (!$this->starts_at || !$this->ends_at) {
            return 0;
        }
        return (int) $this->starts_at->diffInMinutes($this->ends_at);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now())->orderBy('starts_at');
    }

    protected static function booted(): void
    {
        static::created(function (CalendarEvent $event) {
            event(new CalendarEventCreated($event->id, $event->user_id));
        });

        static::updated(function (CalendarEvent $event) {
            event(new CalendarEventUpdated($event->id, $event->user_id));
        });

        static::deleted(function (CalendarEvent $event) {
            event(new CalendarEventDeleted($event->id, $event->user_id));
        });
    }
}
