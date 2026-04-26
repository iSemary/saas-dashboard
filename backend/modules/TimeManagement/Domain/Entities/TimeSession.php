<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\TimeManagement\Domain\Events\TimerStarted;
use Modules\TimeManagement\Domain\Events\TimerStopped;
use Modules\TimeManagement\Domain\Exceptions\TimerAlreadyRunning;
use Modules\Auth\Entities\User;

class TimeSession extends Model
{
    protected $table = 'tm_time_sessions';

    protected $fillable = [
        'tenant_id', 'user_id', 'project_id', 'task_id',
        'started_at', 'stopped_at', 'duration_seconds',
        'is_running', 'description',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
        'duration_seconds' => 'integer',
        'is_running' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function startForUser(string $userId, string $tenantId, ?string $projectId = null, ?string $taskId = null, ?string $description = null): self
    {
        if (static::where('user_id', $userId)->where('is_running', true)->exists()) {
            throw new TimerAlreadyRunning();
        }

        $session = static::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'project_id' => $projectId,
            'task_id' => $taskId,
            'started_at' => now(),
            'is_running' => true,
            'description' => $description,
        ]);

        event(new TimerStarted($session->id, $userId, $projectId, $taskId));

        return $session;
    }

    public function stop(): self
    {
        if (!$this->is_running) {
            return $this;
        }

        $this->stopped_at = now();
        $this->duration_seconds = $this->started_at->diffInSeconds($this->stopped_at);
        $this->is_running = false;
        $this->save();

        event(new TimerStopped($this->id, $this->user_id, $this->duration_seconds));

        return $this;
    }

    public function scopeRunning($query)
    {
        return $query->where('is_running', true);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }
}
