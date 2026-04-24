<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentGatewayLog extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "payment gateway log";
    public $pluralTitle = "payment gateway logs";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_method_id',
        'transaction_id',
        'log_level',
        'operation',
        'request_data',
        'response_data',
        'endpoint_called',
        'http_status',
        'processing_time_ms',
        'error_code',
        'error_message',
        'correlation_id',
        'headers',
        'gateway_request_id',
        'is_webhook',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'http_status' => 'integer',
        'processing_time_ms' => 'integer',
        'headers' => 'array',
        'is_webhook' => 'boolean',
    ];

    /**
     * Get the payment method.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the transaction.
     */
    public function transaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    /**
     * Scope to filter by log level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('log_level', $level);
    }

    /**
     * Scope to filter error logs.
     */
    public function scopeErrors($query)
    {
        return $query->whereIn('log_level', ['error', 'critical']);
    }

    /**
     * Scope to filter by operation.
     */
    public function scopeByOperation($query, $operation)
    {
        return $query->where('operation', $operation);
    }

    /**
     * Scope to filter webhook logs.
     */
    public function scopeWebhooks($query)
    {
        return $query->where('is_webhook', true);
    }

    /**
     * Scope to filter by correlation ID.
     */
    public function scopeByCorrelationId($query, $correlationId)
    {
        return $query->where('correlation_id', $correlationId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Check if this is an error log.
     */
    public function isError()
    {
        return in_array($this->log_level, ['error', 'critical']);
    }

    /**
     * Check if this is a successful operation.
     */
    public function isSuccessful()
    {
        return $this->http_status >= 200 && $this->http_status < 300;
    }

    /**
     * Get formatted processing time.
     */
    public function getFormattedProcessingTime()
    {
        if (!$this->processing_time_ms) {
            return 'N/A';
        }

        if ($this->processing_time_ms < 1000) {
            return $this->processing_time_ms . 'ms';
        }

        return round($this->processing_time_ms / 1000, 2) . 's';
    }

    /**
     * Get sanitized request data (remove sensitive information).
     */
    public function getSanitizedRequestData()
    {
        if (!$this->request_data) {
            return null;
        }

        $data = $this->request_data;
        $sensitiveKeys = [
            'password', 'secret', 'key', 'token', 'api_key', 'private_key',
            'card_number', 'cvv', 'cvc', 'expiry', 'pin'
        ];

        return $this->sanitizeArray($data, $sensitiveKeys);
    }

    /**
     * Get sanitized response data (remove sensitive information).
     */
    public function getSanitizedResponseData()
    {
        if (!$this->response_data) {
            return null;
        }

        $data = $this->response_data;
        $sensitiveKeys = [
            'password', 'secret', 'key', 'token', 'api_key', 'private_key',
            'card_number', 'cvv', 'cvc', 'expiry', 'pin'
        ];

        return $this->sanitizeArray($data, $sensitiveKeys);
    }

    /**
     * Recursively sanitize array by masking sensitive keys.
     */
    protected function sanitizeArray($array, $sensitiveKeys)
    {
        if (!is_array($array)) {
            return $array;
        }

        foreach ($array as $key => $value) {
            $lowerKey = strtolower($key);
            
            // Check if key contains sensitive information
            foreach ($sensitiveKeys as $sensitiveKey) {
                if (strpos($lowerKey, $sensitiveKey) !== false) {
                    $array[$key] = $this->maskValue($value);
                    continue 2;
                }
            }

            // Recursively sanitize nested arrays
            if (is_array($value)) {
                $array[$key] = $this->sanitizeArray($value, $sensitiveKeys);
            }
        }

        return $array;
    }

    /**
     * Mask sensitive values.
     */
    protected function maskValue($value)
    {
        if (!is_string($value)) {
            return '[MASKED]';
        }

        $length = strlen($value);
        
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        if ($length <= 8) {
            return substr($value, 0, 2) . str_repeat('*', $length - 4) . substr($value, -2);
        }

        return substr($value, 0, 4) . str_repeat('*', $length - 8) . substr($value, -4);
    }

    /**
     * Create a log entry with automatic sanitization.
     */
    public static function createLog($paymentMethodId, $level, $operation, $data = [])
    {
        return static::create([
            'payment_method_id' => $paymentMethodId,
            'transaction_id' => $data['transaction_id'] ?? null,
            'log_level' => $level,
            'operation' => $operation,
            'request_data' => $data['request_data'] ?? null,
            'response_data' => $data['response_data'] ?? null,
            'endpoint_called' => $data['endpoint_called'] ?? null,
            'http_status' => $data['http_status'] ?? null,
            'processing_time_ms' => $data['processing_time_ms'] ?? null,
            'error_code' => $data['error_code'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'correlation_id' => $data['correlation_id'] ?? null,
            'headers' => $data['headers'] ?? null,
            'gateway_request_id' => $data['gateway_request_id'] ?? null,
            'is_webhook' => $data['is_webhook'] ?? false,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
        ]);
    }
}
