<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardSwimlane extends Model
{
    protected $table = 'pm_board_swimlanes';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'name',
        'type',
        'value',
        'position',
    ];

    protected $casts = [
        'position' => 'float',
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
