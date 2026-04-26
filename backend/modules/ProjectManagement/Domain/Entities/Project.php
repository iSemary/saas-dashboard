<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Auth\Entities\User;
use Modules\ProjectManagement\Domain\ValueObjects\ProjectStatus;
use Modules\ProjectManagement\Domain\ValueObjects\ProjectHealth;
use Modules\ProjectManagement\Domain\Exceptions\InvalidProjectStatusTransition;
use Modules\ProjectManagement\Domain\Events\ProjectCreated;
use Modules\ProjectManagement\Domain\Events\ProjectStatusChanged;

class Project extends Model
{
    use SoftDeletes;

    protected $table = 'pm_projects';

    protected $fillable = [
        'tenant_id',
        'workspace_id',
        'name',
        'description',
        'status',
        'health',
        'health_score',
        'start_date',
        'end_date',
        'budget',
        'spent',
        'settings',
        'created_by',
    ];

    protected $casts = [
        'settings' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'health_score' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->status)) {
                $project->status = ProjectStatus::PLANNING->value;
            }
            if (empty($project->health)) {
                $project->health = ProjectHealth::ON_TRACK->value;
            }
            if (empty($project->health_score)) {
                $project->health_score = 100.00;
            }
        });

        static::created(function ($project) {
            event(new ProjectCreated($project));
        });
    }

    // ── Relationships ─────────────────────────────────────

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function boardColumns(): HasMany
    {
        return $this->hasMany(BoardColumn::class);
    }

    public function boardSwimlanes(): HasMany
    {
        return $this->hasMany(BoardSwimlane::class);
    }

    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function risks(): HasMany
    {
        return $this->hasMany(Risk::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    public function sprintCycles(): HasMany
    {
        return $this->hasMany(SprintCycle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ProjectStatus::ACTIVE->value);
    }

    // ── Business Methods ───────────────────────────────────

    public function status(): ProjectStatus
    {
        return ProjectStatus::fromString($this->status);
    }

    public function healthStatus(): ProjectHealth
    {
        return ProjectHealth::fromString($this->health);
    }

    public function transitionStatus(ProjectStatus $to): void
    {
        $from = $this->status();

        if (!ProjectStatus::canTransitionFrom($from, $to)) {
            throw new InvalidProjectStatusTransition($from->value, $to->value);
        }

        $oldStatus = $this->status;
        $this->status = $to->value;
        $this->save();

        event(new ProjectStatusChanged($this->id, $from, $to));
    }

    public function activate(): void
    {
        $this->transitionStatus(ProjectStatus::ACTIVE);
    }

    public function pause(): void
    {
        $this->transitionStatus(ProjectStatus::ON_HOLD);
    }

    public function complete(): void
    {
        $this->transitionStatus(ProjectStatus::COMPLETED);
    }

    public function archive(): void
    {
        $this->transitionStatus(ProjectStatus::ARCHIVED);
    }

    public function recalculateHealth(float $score): void
    {
        $this->health_score = $score;
        $this->health = ProjectHealth::fromScore($score)->value;
        $this->save();
    }

    public function isOverdue(): bool
    {
        return $this->end_date !== null
            && $this->end_date->isPast()
            && $this->status !== ProjectStatus::COMPLETED->value;
    }
}
