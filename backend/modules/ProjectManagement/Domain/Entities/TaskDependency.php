<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ProjectManagement\Domain\ValueObjects\DependencyType;

class TaskDependency extends Model
{
    protected $table = 'pm_task_dependencies';

    protected $fillable = [
        'tenant_id',
        'predecessor_id',
        'successor_id',
        'type',
    ];

    public function predecessor(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'predecessor_id');
    }

    public function successor(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'successor_id');
    }

    public function dependencyType(): DependencyType
    {
        return DependencyType::fromString($this->type);
    }
}
