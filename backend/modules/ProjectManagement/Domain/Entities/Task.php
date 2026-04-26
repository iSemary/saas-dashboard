<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Auth\Entities\User;
use Modules\ProjectManagement\Domain\ValueObjects\TaskStatus;
use Modules\ProjectManagement\Domain\ValueObjects\TaskPriority;
use Modules\ProjectManagement\Domain\Exceptions\InvalidTaskStatusTransition;
use Modules\ProjectManagement\Domain\Events\TaskCreated;
use Modules\ProjectManagement\Domain\Events\TaskStatusChanged;
use Modules\ProjectManagement\Domain\Events\TaskMovedToColumn;
use Modules\ProjectManagement\Domain\Events\TaskAssigned;

class Task extends Model
{
    use SoftDeletes;

    protected $table = 'pm_tasks';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'milestone_id',
        'board_column_id',
        'swimlane_id',
        'parent_task_id',
        'title',
        'description',
        'status',
        'priority',
        'type',
        'position',
        'start_date',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'assignee_id',
        'created_by',
    ];

    protected $casts = [
        'position' => 'float',
        'start_date' => 'date',
        'due_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->status)) {
                $task->status = TaskStatus::TODO->value;
            }
            if (empty($task->priority)) {
                $task->priority = TaskPriority::MEDIUM->value;
            }
        });

        static::created(function ($task) {
            event(new TaskCreated($task->id, $task->project_id));
        });
    }

    // ── Relationships ─────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function boardColumn(): BelongsTo
    {
        return $this->belongsTo(BoardColumn::class);
    }

    public function swimlane(): BelongsTo
    {
        return $this->belongsTo(BoardSwimlane::class);
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'pm_task_labels', 'task_id', 'label_id');
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'predecessor_id');
    }

    public function dependants(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'successor_id');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForProject($query, string $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    // ── Business Methods ───────────────────────────────────

    public function taskStatus(): TaskStatus
    {
        return TaskStatus::fromString($this->status);
    }

    public function taskPriority(): TaskPriority
    {
        return TaskPriority::fromString($this->priority);
    }

    public function transitionStatus(TaskStatus $to): void
    {
        $from = $this->taskStatus();

        if (!TaskStatus::canTransitionFrom($from, $to)) {
            throw new InvalidTaskStatusTransition($from->value, $to->value);
        }

        $this->status = $to->value;
        $this->save();

        event(new TaskStatusChanged($this->id, $this->project_id, $from, $to));
    }

    public function moveToColumn(string $columnId): void
    {
        $oldColumnId = $this->board_column_id;
        $this->board_column_id = $columnId;
        $this->save();

        event(new TaskMovedToColumn($this->id, $this->project_id, $oldColumnId, $columnId));
    }

    public function assignTo(?string $userId): void
    {
        $this->assignee_id = $userId;
        $this->save();

        event(new TaskAssigned($this->id, $this->project_id, $userId));
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && $this->status !== TaskStatus::DONE->value;
    }

    public function isSubTask(): bool
    {
        return $this->parent_task_id !== null;
    }
}
