<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Entities\User;

class SurveyWebhook extends Model
{
    use SoftDeletes;

    protected $table = 'survey_webhooks';

    protected $fillable = [
        'survey_id',
        'name',
        'url',
        'secret',
        'events',
        'is_active',
        'last_triggered_at',
        'created_by',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function toggle(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    public function regenerateSecret(): void
    {
        $this->update(['secret' => $this->generateSecret()]);
    }

    public function recordTrigger(): void
    {
        $this->update(['last_triggered_at' => now()]);
    }

    public function shouldTrigger(string $event): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $events = $this->events ?? [];
        return in_array($event, $events, true) || in_array('*', $events, true);
    }

    public function generatePayload(array $data): array
    {
        return [
            'event' => $data['event'] ?? 'unknown',
            'timestamp' => now()->toIso8601String(),
            'survey_id' => $this->survey_id,
            'data' => $data,
        ];
    }

    public function signPayload(array $payload): string
    {
        $jsonPayload = json_encode($payload);
        return hash_hmac('sha256', $jsonPayload, $this->secret);
    }

    public function verifySignature(string $payload, string $signature): bool
    {
        $expected = hash_hmac('sha256', $payload, $this->secret);
        return hash_equals($expected, $signature);
    }

    private function generateSecret(): string
    {
        return bin2hex(random_bytes(32));
    }
}
