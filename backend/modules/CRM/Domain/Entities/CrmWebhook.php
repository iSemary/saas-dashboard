<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

/**
 * Outbound webhook for CRM events.
 */
class CrmWebhook extends Model
{
    protected $table = 'crm_webhooks';

    protected $fillable = [
        'name',
        'url',
        'events',
        'secret',
        'is_active',
        'last_triggered_at',
        'retry_count',
        'created_by',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    /**
     * Get the user who created this webhook.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Get active webhooks.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get webhooks that subscribe to a specific event.
     */
    public function scopeForEvent($query, string $event)
    {
        return $query->whereJsonContains('events', $event);
    }

    /**
     * Business method: Enable the webhook.
     */
    public function enable(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Business method: Disable the webhook.
     */
    public function disable(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Toggle active state.
     */
    public function toggle(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Record a trigger attempt.
     */
    public function recordTrigger(): void
    {
        $this->update([
            'last_triggered_at' => now(),
            'retry_count' => 0,
        ]);
    }

    /**
     * Increment retry count.
     */
    public function incrementRetry(): void
    {
        $this->increment('retry_count');
    }

    /**
     * Check if webhook should be disabled due to max retries.
     */
    public function shouldDisable(): bool
    {
        $maxRetries = config('crm.webhook.max_retries', 5);

        return $this->retry_count >= $maxRetries;
    }

    /**
     * Get headers for webhook request.
     */
    public function getHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'CRM-Webhook/1.0',
        ];

        if ($this->secret) {
            $headers['X-Webhook-Signature'] = $this->generateSignature('');
        }

        return $headers;
    }

    /**
     * Generate signature for payload.
     */
    public function generateSignature(string $payload): string
    {
        if (!$this->secret) {
            return '';
        }

        return hash_hmac('sha256', $payload, $this->secret);
    }

    /**
     * Verify signature.
     */
    public function verifySignature(string $payload, string $signature): bool
    {
        if (!$this->secret) {
            return true;
        }

        $expected = $this->generateSignature($payload);

        return hash_equals($expected, $signature);
    }

    /**
     * Check if webhook listens to a specific event.
     */
    public function listensTo(string $event): bool
    {
        return in_array($event, $this->events ?? [], true);
    }
}
