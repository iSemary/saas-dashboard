<?php

namespace Modules\API\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\API\Database\Factories\ApiKeyFactory;

class ApiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'key',
        'secret',
        'user_id',
        'status',
        'permissions',
        'scopes',
        'last_used_at',
        'expires_at',
        'ip_whitelist',
        'rate_limit',
        'rate_limit_period',
        'description',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'permissions' => 'array',
        'scopes' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    protected $hidden = [
        'secret',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(ApiLog::class, 'api_key_id', 'key');
    }

    public function rateLimits()
    {
        return $this->hasMany(ApiRateLimit::class, 'identifier', 'key');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && $this->isNotExpired;
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsNotExpiredAttribute()
    {
        return !$this->is_expired;
    }

    public function getMaskedKeyAttribute()
    {
        return substr($this->key, 0, 8) . '...' . substr($this->key, -4);
    }

    // Methods
    public function isAllowedIp($ip)
    {
        if (!$this->ip_whitelist) {
            return true;
        }

        $allowedIps = explode(',', $this->ip_whitelist);
        return in_array($ip, array_map('trim', $allowedIps));
    }

    public function hasPermission($permission)
    {
        if (!$this->permissions) {
            return true; // No specific permissions means all allowed
        }

        return in_array($permission, $this->permissions);
    }

    public function hasScope($scope)
    {
        if (!$this->scopes) {
            return true; // No specific scopes means all allowed
        }

        return in_array($scope, $this->scopes);
    }

    public function updateLastUsed()
    {
        $this->update(['last_used_at' => now()]);
    }

    public function suspend($reason = null)
    {
        $this->update([
            'status' => 'suspended',
            'custom_fields' => array_merge($this->custom_fields ?? [], [
                'suspension_reason' => $reason,
                'suspended_at' => now()->toISOString(),
            ]),
        ]);
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'custom_fields' => array_merge($this->custom_fields ?? [], [
                'activated_at' => now()->toISOString(),
            ]),
        ]);
    }

    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
    }

    public function regenerateSecret()
    {
        $this->update([
            'secret' => bin2hex(random_bytes(32)),
        ]);
    }

    public function getRateLimitInfo()
    {
        return [
            'limit' => $this->rate_limit,
            'period' => $this->rate_limit_period,
            'remaining' => $this->getRemainingRequests(),
        ];
    }

    public function getRemainingRequests()
    {
        // This would typically be calculated based on current usage
        // For now, return the limit
        return $this->rate_limit;
    }

    protected static function newFactory(): ApiKeyFactory
    {
        return ApiKeyFactory::new();
    }
}
