<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardColumn extends Model
{
    protected $table = 'pm_board_columns';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'name',
        'type',
        'status_mapping',
        'wip_limit',
        'position',
    ];

    protected $casts = [
        'position' => 'float',
        'wip_limit' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
