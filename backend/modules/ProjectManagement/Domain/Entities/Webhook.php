<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class Webhook extends Model
{
    protected $table = 'pm_webhooks';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'name',
        'url',
        'events',
        'is_active',
        'secret',
        'created_by',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function toggle(): void
    {
        $this->is_active = !$this->is_active;
        $this->save();
    }

    public function regenerateSecret(): string
    {
        $this->secret = bin2hex(random_bytes(32));
        $this->save();
        return $this->secret;
    }
}
