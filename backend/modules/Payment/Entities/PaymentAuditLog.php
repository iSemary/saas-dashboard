<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation',
        'entity_type',
        'entity_id',
        'user_id',
        'ip_address',
        'user_agent',
        'session_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who performed the operation.
     */
    public function user()
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class);
    }

    /**
     * Get the related entity (polymorphic).
     */
    public function entity()
    {
        if ($this->entity_type && $this->entity_id) {
            $modelClass = "Modules\\Payment\\Entities\\{$this->entity_type}";
            if (class_exists($modelClass)) {
                return $this->belongsTo($modelClass, 'entity_id');
            }
        }
        return null;
    }

    /**
     * Scope for filtering by operation type.
     */
    public function scopeByOperation($query, string $operation)
    {
        return $query->where('operation', 'like', "%{$operation}%");
    }

    /**
     * Scope for filtering by entity type.
     */
    public function scopeByEntityType($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope for filtering by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted operation name.
     */
    public function getFormattedOperationAttribute(): string
    {
        return ucwords(str_replace(['.', '_'], ' ', $this->operation));
    }

    /**
     * Get masked sensitive data.
     */
    public function getMaskedDataAttribute(): array
    {
        if (!$this->data) {
            return [];
        }

        $securityService = app(\Modules\Payment\Services\PaymentSecurityService::class);
        return $securityService->sanitizeForAudit($this->data);
    }
}
