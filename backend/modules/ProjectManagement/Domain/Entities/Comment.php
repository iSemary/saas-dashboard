<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class Comment extends Model
{
    protected $table = 'pm_comments';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'commentable_type',
        'commentable_id',
        'content',
        'user_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }
}
