<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;
use Modules\CRM\Database\Factories\ActivityFactory;
use Modules\CRM\Domain\ValueObjects\ActivityType;
use Modules\CRM\Domain\ValueObjects\ActivityStatus;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_activities';

    protected $fillable = [
        'subject',
        'description',
        'type',
        'status',
        'due_date',
        'completed_at',
        'related_type',
        'related_id',
        'assigned_to',
        'created_by',
        'outcome',
        'custom_fields',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($activity) {
            if (empty($activity->type)) {
                $activity->type = ActivityType::TASK->value;
            }
            if (empty($activity->status)) {
                $activity->status = ActivityStatus::PLANNED->value;
            }
        });
    }

    // ── Relationships ─────────────────────────────────────

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function related(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)])
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeForToday($query)
    {
        return $query->whereDate('due_date', today())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    // ── Business Methods ─────────────────────────────────

    /**
     * Mark activity as completed.
     */
    public function complete(?string $outcome = null): void
    {
        $this->update([
            'status' => ActivityStatus::COMPLETED->value,
            'completed_at' => now(),
            'outcome' => $outcome,
        ]);

        event(new \Modules\CRM\Domain\Events\ActivityCompleted($this));
    }

    /**
     * Mark activity as in progress.
     */
    public function start(): void
    {
        $this->update(['status' => ActivityStatus::IN_PROGRESS->value]);
    }

    /**
     * Cancel activity.
     */
    public function cancel(): void
    {
        $this->update(['status' => ActivityStatus::CANCELLED->value]);
    }

    /**
     * Reschedule activity.
     */
    public function reschedule(\DateTimeInterface $newDueDate): void
    {
        $this->update([
            'due_date' => $newDueDate,
            'status' => ActivityStatus::PLANNED->value,
        ]);
    }

    /**
     * Assign to user.
     */
    public function assignTo(int $userId): void
    {
        $this->update(['assigned_to' => $userId]);
    }

    /**
     * Check if activity is overdue.
     */
    public function isOverdue(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'], true)) {
            return false;
        }

        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Check if activity is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === ActivityStatus::COMPLETED->value;
    }

    /**
     * Check if activity is today.
     */
    public function isToday(): bool
    {
        return $this->due_date && $this->due_date->isToday();
    }

    /**
     * Get duration in minutes (if completed).
     */
    public function durationMinutes(): ?int
    {
        if (!$this->completed_at || !$this->due_date) {
            return null;
        }

        return (int) $this->due_date->diffInMinutes($this->completed_at);
    }

    // ── Accessors ─────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return ActivityType::fromString($this->type)->label();
    }

    public function getTypeIconAttribute(): string
    {
        return ActivityType::fromString($this->type)->icon();
    }

    public function getStatusLabelAttribute(): string
    {
        return ActivityStatus::fromString($this->status)->label();
    }

    public function getStatusColorAttribute(): string
    {
        return ActivityStatus::fromString($this->status)->color();
    }

    protected static function newFactory()
    {
        return ActivityFactory::new();
    }
}
