<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Chargeback extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "chargeback";
    public $pluralTitle = "chargebacks";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chargeback_id',
        'transaction_id',
        'amount',
        'fee',
        'reason_code',
        'reason_description',
        'status',
        'gateway_case_id',
        'evidence_due_date',
        'evidence_submitted',
        'resolution',
        'resolution_notes',
        'liability_shift_amount',
        'gateway_response',
        'received_at',
        'resolved_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'evidence_due_date' => 'date',
        'evidence_submitted' => 'array',
        'liability_shift_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'received_at' => 'datetime',
        'resolved_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the transaction.
     */
    public function transaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter active chargebacks.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['received', 'under_review', 'disputed']);
    }

    /**
     * Scope to filter resolved chargebacks.
     */
    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['won', 'lost', 'accepted']);
    }

    /**
     * Scope to filter by resolution.
     */
    public function scopeByResolution($query, $resolution)
    {
        return $query->where('resolution', $resolution);
    }

    /**
     * Scope to filter chargebacks with pending evidence.
     */
    public function scopePendingEvidence($query)
    {
        return $query->where('status', 'under_review')
                     ->where('evidence_due_date', '>=', now()->toDateString());
    }

    /**
     * Scope to filter overdue evidence.
     */
    public function scopeOverdueEvidence($query)
    {
        return $query->where('status', 'under_review')
                     ->where('evidence_due_date', '<', now()->toDateString());
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('received_at', [$startDate, $endDate]);
    }

    /**
     * Check if chargeback is active.
     */
    public function isActive()
    {
        return in_array($this->status, ['received', 'under_review', 'disputed']);
    }

    /**
     * Check if chargeback is resolved.
     */
    public function isResolved()
    {
        return in_array($this->status, ['won', 'lost', 'accepted']);
    }

    /**
     * Check if evidence is due.
     */
    public function isEvidenceDue()
    {
        return $this->status === 'under_review' && 
               $this->evidence_due_date && 
               $this->evidence_due_date->isFuture();
    }

    /**
     * Check if evidence is overdue.
     */
    public function isEvidenceOverdue()
    {
        return $this->status === 'under_review' && 
               $this->evidence_due_date && 
               $this->evidence_due_date->isPast();
    }

    /**
     * Check if chargeback was won.
     */
    public function isWon()
    {
        return $this->resolution === 'won';
    }

    /**
     * Check if chargeback was lost.
     */
    public function isLost()
    {
        return $this->resolution === 'lost';
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        $currency = $this->transaction->currency;
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
     * Get total impact (amount + fee).
     */
    public function getTotalImpactAttribute()
    {
        return $this->amount + $this->fee;
    }

    /**
     * Get formatted total impact with currency.
     */
    public function getFormattedTotalImpactAttribute()
    {
        $currency = $this->transaction->currency;
        $totalImpact = $this->getTotalImpactAttribute();
        
        if (!$currency) {
            return number_format($totalImpact, 2);
        }

        $formatted = number_format($totalImpact, $currency->decimal_places);
        
        if ($currency->symbol_position === 'left') {
            return $currency->symbol . ' ' . $formatted;
        } else {
            return $formatted . ' ' . $currency->symbol;
        }
    }

    /**
     * Get days until evidence due date.
     */
    public function getDaysUntilEvidenceDueAttribute()
    {
        if (!$this->evidence_due_date) {
            return null;
        }

        return now()->diffInDays($this->evidence_due_date, false);
    }

    /**
     * Submit evidence for the chargeback.
     */
    public function submitEvidence($evidence, $submittedBy = null)
    {
        $this->evidence_submitted = $evidence;
        $this->status = 'disputed';
        
        $metadata = $this->metadata ?? [];
        $metadata['evidence_submitted_at'] = now()->toISOString();
        
        if ($submittedBy) {
            $metadata['evidence_submitted_by'] = $submittedBy;
        }
        
        $this->metadata = $metadata;
        $this->save();

        return $this;
    }

    /**
     * Accept the chargeback.
     */
    public function accept($notes = null)
    {
        $this->status = 'accepted';
        $this->resolution = 'lost';
        $this->resolved_at = now();
        
        if ($notes) {
            $this->resolution_notes = $notes;
        }

        $this->save();

        // Update transaction status
        $this->transaction->update(['status' => 'charged_back']);

        return $this;
    }

    /**
     * Mark chargeback as won.
     */
    public function markAsWon($notes = null, $gatewayResponse = null)
    {
        $this->status = 'won';
        $this->resolution = 'won';
        $this->resolved_at = now();
        
        if ($notes) {
            $this->resolution_notes = $notes;
        }

        if ($gatewayResponse) {
            $this->gateway_response = $gatewayResponse;
        }

        $this->save();

        return $this;
    }

    /**
     * Mark chargeback as lost.
     */
    public function markAsLost($notes = null, $gatewayResponse = null)
    {
        $this->status = 'lost';
        $this->resolution = 'lost';
        $this->resolved_at = now();
        
        if ($notes) {
            $this->resolution_notes = $notes;
        }

        if ($gatewayResponse) {
            $this->gateway_response = $gatewayResponse;
        }

        $this->save();

        // Update transaction status
        $this->transaction->update(['status' => 'charged_back']);

        return $this;
    }

    /**
     * Get human-readable status.
     */
    public function getHumanReadableStatusAttribute()
    {
        $statuses = [
            'received' => 'Received',
            'under_review' => 'Under Review',
            'disputed' => 'Disputed',
            'accepted' => 'Accepted',
            'won' => 'Won',
            'lost' => 'Lost',
            'expired' => 'Expired',
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get chargeback timeline.
     */
    public function getTimelineAttribute()
    {
        $timeline = [
            [
                'event' => 'Chargeback Received',
                'timestamp' => $this->received_at ?? $this->created_at,
                'status' => 'completed',
            ],
        ];

        if ($this->evidence_submitted) {
            $evidenceDate = $this->metadata['evidence_submitted_at'] ?? null;
            if ($evidenceDate) {
                $timeline[] = [
                    'event' => 'Evidence Submitted',
                    'timestamp' => \Carbon\Carbon::parse($evidenceDate),
                    'status' => 'completed',
                ];
            }
        }

        if ($this->resolved_at) {
            $timeline[] = [
                'event' => 'Chargeback Resolved',
                'timestamp' => $this->resolved_at,
                'status' => $this->isWon() ? 'won' : 'lost',
            ];
        }

        return $timeline;
    }

    /**
     * Get evidence requirements based on reason code.
     */
    public function getEvidenceRequirements()
    {
        // This would typically be mapped from reason codes to required evidence
        $requirements = [
            'fraud' => [
                'proof_of_delivery',
                'customer_communication',
                'billing_address_verification',
                'cvv_verification',
            ],
            'authorization' => [
                'authorization_proof',
                'transaction_receipt',
                'customer_agreement',
            ],
            'duplicate_processing' => [
                'proof_of_single_transaction',
                'transaction_logs',
            ],
            'credit_not_processed' => [
                'refund_proof',
                'credit_documentation',
            ],
            'cancelled_recurring' => [
                'cancellation_proof',
                'subscription_terms',
            ],
        ];

        // Map reason code to category (this would be more sophisticated in practice)
        $category = $this->mapReasonCodeToCategory($this->reason_code);
        
        return $requirements[$category] ?? ['general_evidence'];
    }

    /**
     * Map reason code to evidence category.
     */
    protected function mapReasonCodeToCategory($reasonCode)
    {
        // This is a simplified mapping - in practice, you'd have comprehensive mappings
        // for different card networks (Visa, Mastercard, etc.)
        $mappings = [
            '4855' => 'fraud', // Visa - Goods/Services Not Received
            '4837' => 'fraud', // Visa - No Cardholder Authorization
            '4834' => 'duplicate_processing', // Visa - Duplicate Processing
            '4853' => 'credit_not_processed', // Visa - Cardholder Dispute
        ];

        return $mappings[$reasonCode] ?? 'general';
    }
}
