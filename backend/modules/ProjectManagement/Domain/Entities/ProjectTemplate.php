<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class ProjectTemplate extends Model
{
    protected $table = 'pm_project_templates';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'structure',
        'is_public',
        'created_by',
    ];

    protected $casts = [
        'structure' => 'array',
        'is_public' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
