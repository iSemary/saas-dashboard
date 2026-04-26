<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;
use Modules\ProjectManagement\Domain\Events\MilestoneCompleted;

class Milestone extends Model
{
    use SoftDeletes;

    protected $table = 'pm_milestones';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'name',
        'description',
        'due_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function complete(): void
    {
        $this->status = 'completed';
        $this->save();

        event(new MilestoneCompleted($this->id, $this->project_id));
    }
}
