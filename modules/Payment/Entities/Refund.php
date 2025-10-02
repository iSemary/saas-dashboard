<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Refund extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "refund";
    public $pluralTitle = "refunds";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'refund_id',
        'original_transaction_id',
        'refund_transaction_id',
        'amount',
        'fee_refunded',
        'refund_type',
        'reason',
        'reason_details',
        'status',
        'gateway_refund_id',
        'gateway_response',
        'initiated_by',
        'processed_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'fee_refunded' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the original transaction.
     */
    public function originalTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'original_transaction_id');
    }

    /**
     * Get the refund transaction.
     */
    public function refundTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'refund_transaction_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter completed refunds.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to filter pending refunds.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'processing']);
    }

    /**
     * Scope to filter failed refunds.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to filter by refund type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('refund_type', $type);
    }

    /**
     * Scope to filter by reason.
     */
    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Check if refund is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if refund is pending.
     */
    public function isPending()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Check if refund is failed.
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if this is a full refund.
     */
    public function isFullRefund()
    {
        return $this->refund_type === 'full' || 
               $this->amount >= $this->originalTransaction->amount;
    }

    /**
     * Check if this is a partial refund.
     */
    public function isPartialRefund()
    {
        return $this->refund_type === 'partial' && 
               $this->amount < $this->originalTransaction->amount;
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        $currency = $this->originalTransaction->currency;
        if (!$currency) {
            return number_format($this->amount, 2);
        }

        $formatted = number_format($this->amount, $currency->decimal_places);
        
        if ($currency->symbol_position === 'left') {
            return $currency->symbol . ' ' . $formatted;
        } else {
            return $formatted . ' ' . $currency->symbol;
        }
    }

    /**
     * Get refund percentage of original transaction.
     */
    public function getRefundPercentageAttribute()
    {
        if (!$this->originalTransaction || $this->originalTransaction->amount == 0) {
            return 0;
        }

        return round(($this->amount / $this->originalTransaction->amount) * 100, 2);
    }

    /**
     * Get net refund amount (after fees).
     */
    public function getNetRefundAmountAttribute()
    {
        return $this->amount - $this->fee_refunded;
    }

    /**
     * Mark refund as completed.
     */
    public function markAsCompleted($gatewayResponse = null)
    {
        $this->status = 'completed';
        $this->processed_at = now();
        
        if ($gatewayResponse) {
            $this->gateway_response = $gatewayResponse;
        }

        $this->save();

        // Update original transaction status if fully refunded
        if ($this->isFullRefund()) {
            $this->originalTransaction->update(['status' => 'refunded']);
        } else {
            $this->originalTransaction->update(['status' => 'partially_refunded']);
        }

        return $this;
    }

    /**
     * Mark refund as failed.
     */
    public function markAsFailed($errorMessage = null, $gatewayResponse = null)
    {
        $this->status = 'failed';
        
        if ($errorMessage) {
            $metadata = $this->metadata ?? [];
            $metadata['error_message'] = $errorMessage;
            $this->metadata = $metadata;
        }

        if ($gatewayResponse) {
            $this->gateway_response = $gatewayResponse;
        }

        $this->save();

        return $this;
    }

    /**
     * Calculate processing time.
     */
    public function getProcessingTimeAttribute()
    {
        if (!$this->processed_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->processed_at);
    }

    /**
     * Get human-readable reason.
     */
    public function getHumanReadableReasonAttribute()
    {
        $reasons = [
            'requested_by_customer' => 'Customer Request',
            'duplicate' => 'Duplicate Transaction',
            'fraudulent' => 'Fraudulent Transaction',
            'subscription_cancellation' => 'Subscription Cancellation',
            'other' => 'Other',
        ];

        return $reasons[$this->reason] ?? ucfirst(str_replace('_', ' ', $this->reason));
    }

    /**
     * Check if refund can be cancelled.
     */
    public function canBeCancelled()
    {
        return $this->status === 'pending';
    }

    /**
     * Cancel the refund.
     */
    public function cancel($reason = null)
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = 'cancelled';
        
        if ($reason) {
            $metadata = $this->metadata ?? [];
            $metadata['cancellation_reason'] = $reason;
            $this->metadata = $metadata;
        }

        $this->save();

        return true;
    }

    /**
     * Get refund timeline.
     */
    public function getTimelineAttribute()
    {
        $timeline = [
            [
                'event' => 'Refund Initiated',
                'timestamp' => $this->created_at,
                'status' => 'completed',
            ],
        ];

        if ($this->processed_at) {
            $timeline[] = [
                'event' => 'Refund Processed',
                'timestamp' => $this->processed_at,
                'status' => $this->status === 'completed' ? 'completed' : 'failed',
            ];
        }

        return $timeline;
    }
}
