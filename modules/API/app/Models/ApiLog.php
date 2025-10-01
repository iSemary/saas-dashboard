<?php

namespace Modules\API\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Entities\User;
use Modules\API\Database\Factories\ApiLogFactory;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_key_id',
        'user_id',
        'method',
        'endpoint',
        'ip_address',
        'user_agent',
        'status_code',
        'response_time_ms',
        'request_headers',
        'request_body',
        'response_headers',
        'response_body',
        'error_message',
        'metadata',
        'logged_at',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'request_body' => 'array',
        'response_headers' => 'array',
        'metadata' => 'array',
        'logged_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class, 'api_key_id', 'key');
    }

    // Scopes
    public function scopeByApiKey($query, $apiKeyId)
    {
        return $query->where('api_key_id', $apiKeyId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', strtoupper($method));
    }

    public function scopeByEndpoint($query, $endpoint)
    {
        return $query->where('endpoint', 'like', "%{$endpoint}%");
    }

    public function scopeByStatusCode($query, $statusCode)
    {
        return $query->where('status_code', $statusCode);
    }

    public function scopeSuccessful($query)
    {
        return $query->whereBetween('status_code', [200, 299]);
    }

    public function scopeFailed($query)
    {
        return $query->where(function ($q) {
            $q->where('status_code', '>=', 400)
              ->orWhereNotNull('error_message');
        });
    }

    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('logged_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('logged_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('logged_at', now()->month)
                    ->whereYear('logged_at', now()->year);
    }

    public function scopeSlowRequests($query, $threshold = 1000)
    {
        return $query->where('response_time_ms', '>', $threshold);
    }

    // Accessors
    public function getIsSuccessfulAttribute()
    {
        return $this->status_code >= 200 && $this->status_code < 300;
    }

    public function getIsFailedAttribute()
    {
        return $this->status_code >= 400 || !is_null($this->error_message);
    }

    public function getIsSlowAttribute()
    {
        return $this->response_time_ms > 1000; // 1 second
    }

    public function getFormattedResponseTimeAttribute()
    {
        if (!$this->response_time_ms) {
            return 'N/A';
        }

        if ($this->response_time_ms < 1000) {
            return $this->response_time_ms . 'ms';
        }

        return round($this->response_time_ms / 1000, 2) . 's';
    }

    public function getStatusClassAttribute()
    {
        if ($this->is_successful) {
            return 'success';
        }

        if ($this->status_code >= 400 && $this->status_code < 500) {
            return 'warning';
        }

        if ($this->status_code >= 500) {
            return 'danger';
        }

        return 'info';
    }

    // Methods
    public function getRequestSize()
    {
        if (!$this->request_body) {
            return 0;
        }

        return strlen(json_encode($this->request_body));
    }

    public function getResponseSize()
    {
        if (!$this->response_body) {
            return 0;
        }

        return strlen($this->response_body);
    }

    public function getFormattedRequestSize()
    {
        $size = $this->getRequestSize();
        return $this->formatBytes($size);
    }

    public function getFormattedResponseSize()
    {
        $size = $this->getResponseSize();
        return $this->formatBytes($size);
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    public function getEndpointParts()
    {
        $parts = explode('/', trim($this->endpoint, '/'));
        return array_filter($parts);
    }

    public function getModuleFromEndpoint()
    {
        $parts = $this->getEndpointParts();
        return $parts[0] ?? 'unknown';
    }

    public function getActionFromEndpoint()
    {
        $parts = $this->getEndpointParts();
        return end($parts) ?: 'unknown';
    }

    protected static function newFactory(): ApiLogFactory
    {
        return ApiLogFactory::new();
    }
}
