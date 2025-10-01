<?php

namespace Modules\API\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\API\Database\Factories\ApiRateLimitFactory;

class ApiRateLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'type',
        'endpoint',
        'requests_count',
        'window_start',
        'limit',
        'period',
        'reset_at',
        'is_blocked',
        'blocked_until',
        'block_reason',
        'custom_fields',
    ];

    protected $casts = [
        'window_start' => 'datetime',
        'reset_at' => 'datetime',
        'is_blocked' => 'boolean',
        'blocked_until' => 'datetime',
        'custom_fields' => 'array',
    ];

    // Scopes
    public function scopeByIdentifier($query, $identifier, $type = null)
    {
        $query = $query->where('identifier', $identifier);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query;
    }

    public function scopeByEndpoint($query, $endpoint)
    {
        return $query->where('endpoint', $endpoint);
    }

    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_blocked', false);
    }

    public function scopeExpired($query)
    {
        return $query->where('reset_at', '<', now());
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return $this->reset_at->isPast();
    }

    public function getRemainingRequestsAttribute()
    {
        return max(0, $this->limit - $this->requests_count);
    }

    public function getIsLimitExceededAttribute()
    {
        return $this->requests_count >= $this->limit;
    }

    // Methods
    public function incrementRequests()
    {
        $this->increment('requests_count');
    }

    public function resetWindow()
    {
        $now = now();
        $this->update([
            'requests_count' => 0,
            'window_start' => $now,
            'reset_at' => $this->calculateResetTime($now),
        ]);
    }

    public function block($reason = null, $until = null)
    {
        $this->update([
            'is_blocked' => true,
            'block_reason' => $reason,
            'blocked_until' => $until ?? now()->addHours(24),
        ]);
    }

    public function unblock()
    {
        $this->update([
            'is_blocked' => false,
            'block_reason' => null,
            'blocked_until' => null,
        ]);
    }

    public function isBlocked()
    {
        if (!$this->is_blocked) {
            return false;
        }

        if ($this->blocked_until && $this->blocked_until->isPast()) {
            $this->unblock();
            return false;
        }

        return true;
    }

    public function canMakeRequest()
    {
        if ($this->isBlocked()) {
            return false;
        }

        if ($this->is_expired) {
            $this->resetWindow();
        }

        return !$this->is_limit_exceeded;
    }

    private function calculateResetTime($startTime)
    {
        switch ($this->period) {
            case 'minute':
                return $startTime->addMinute();
            case 'hour':
                return $startTime->addHour();
            case 'day':
                return $startTime->addDay();
            case 'week':
                return $startTime->addWeek();
            case 'month':
                return $startTime->addMonth();
            default:
                return $startTime->addHour();
        }
    }

    protected static function newFactory(): ApiRateLimitFactory
    {
        return ApiRateLimitFactory::new();
    }
}
