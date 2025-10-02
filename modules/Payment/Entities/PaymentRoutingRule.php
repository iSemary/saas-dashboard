<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentRoutingRule extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "payment routing rule";
    public $pluralTitle = "payment routing rules";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'conditions',
        'priority',
        'target_payment_method_id',
        'fallback_payment_method_id',
        'rule_type',
        'traffic_percentage',
        'time_restrictions',
        'amount_restrictions',
        'geographic_restrictions',
        'customer_segment_restrictions',
        'is_active',
        'effective_from',
        'effective_until',
        'success_count',
        'failure_count',
        'success_rate',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'conditions' => 'array',
        'priority' => 'integer',
        'traffic_percentage' => 'decimal:2',
        'time_restrictions' => 'array',
        'amount_restrictions' => 'array',
        'geographic_restrictions' => 'array',
        'customer_segment_restrictions' => 'array',
        'is_active' => 'boolean',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
        'success_count' => 'integer',
        'failure_count' => 'integer',
        'success_rate' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the target payment method.
     */
    public function targetPaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'target_payment_method_id');
    }

    /**
     * Get the fallback payment method.
     */
    public function fallbackPaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'fallback_payment_method_id');
    }

    /**
     * Scope to filter active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by rule type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('rule_type', $type);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrderedByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Scope to filter effective rules.
     */
    public function scopeEffective($query, $date = null)
    {
        $date = $date ?? now();
        
        return $query->where(function ($q) use ($date) {
            $q->whereNull('effective_from')
              ->orWhere('effective_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('effective_until')
              ->orWhere('effective_until', '>=', $date);
        });
    }

    /**
     * Check if rule matches the given transaction context.
     */
    public function matches($context)
    {
        if (!$this->is_active || !$this->isEffective()) {
            return false;
        }

        // Check traffic percentage (for A/B testing)
        if ($this->traffic_percentage < 100) {
            $hash = crc32($context['customer_id'] ?? '' . $this->id);
            $percentage = abs($hash) % 100;
            if ($percentage >= $this->traffic_percentage) {
                return false;
            }
        }

        // Check time restrictions
        if (!$this->matchesTimeRestrictions()) {
            return false;
        }

        // Check amount restrictions
        if (!$this->matchesAmountRestrictions($context['amount'] ?? 0)) {
            return false;
        }

        // Check geographic restrictions
        if (!$this->matchesGeographicRestrictions($context['country'] ?? null)) {
            return false;
        }

        // Check customer segment restrictions
        if (!$this->matchesCustomerSegmentRestrictions($context['customer_segment'] ?? 'all')) {
            return false;
        }

        // Check custom conditions
        return $this->matchesCustomConditions($context);
    }

    /**
     * Check if rule is currently effective.
     */
    public function isEffective($date = null)
    {
        $date = $date ?? now();
        
        if ($this->effective_from && $date->lt($this->effective_from)) {
            return false;
        }
        
        if ($this->effective_until && $date->gt($this->effective_until)) {
            return false;
        }
        
        return true;
    }

    /**
     * Check time restrictions.
     */
    protected function matchesTimeRestrictions()
    {
        if (!$this->time_restrictions) {
            return true;
        }

        $now = now();
        $restrictions = $this->time_restrictions;

        // Check day of week
        if (isset($restrictions['days_of_week'])) {
            $currentDay = $now->dayOfWeek; // 0 = Sunday, 6 = Saturday
            if (!in_array($currentDay, $restrictions['days_of_week'])) {
                return false;
            }
        }

        // Check time of day
        if (isset($restrictions['time_range'])) {
            $currentTime = $now->format('H:i');
            $startTime = $restrictions['time_range']['start'] ?? '00:00';
            $endTime = $restrictions['time_range']['end'] ?? '23:59';
            
            if ($currentTime < $startTime || $currentTime > $endTime) {
                return false;
            }
        }

        // Check timezone
        if (isset($restrictions['timezone'])) {
            $userTimezone = $restrictions['timezone'];
            $userTime = $now->setTimezone($userTimezone);
            
            if (isset($restrictions['user_time_range'])) {
                $userCurrentTime = $userTime->format('H:i');
                $userStartTime = $restrictions['user_time_range']['start'] ?? '00:00';
                $userEndTime = $restrictions['user_time_range']['end'] ?? '23:59';
                
                if ($userCurrentTime < $userStartTime || $userCurrentTime > $userEndTime) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check amount restrictions.
     */
    protected function matchesAmountRestrictions($amount)
    {
        if (!$this->amount_restrictions) {
            return true;
        }

        $restrictions = $this->amount_restrictions;

        if (isset($restrictions['min_amount']) && $amount < $restrictions['min_amount']) {
            return false;
        }

        if (isset($restrictions['max_amount']) && $amount > $restrictions['max_amount']) {
            return false;
        }

        return true;
    }

    /**
     * Check geographic restrictions.
     */
    protected function matchesGeographicRestrictions($country)
    {
        if (!$this->geographic_restrictions) {
            return true;
        }

        $restrictions = $this->geographic_restrictions;

        // Check allowed countries
        if (isset($restrictions['allowed_countries'])) {
            return in_array($country, $restrictions['allowed_countries']);
        }

        // Check blocked countries
        if (isset($restrictions['blocked_countries'])) {
            return !in_array($country, $restrictions['blocked_countries']);
        }

        return true;
    }

    /**
     * Check customer segment restrictions.
     */
    protected function matchesCustomerSegmentRestrictions($customerSegment)
    {
        if (!$this->customer_segment_restrictions) {
            return true;
        }

        $restrictions = $this->customer_segment_restrictions;

        if (isset($restrictions['allowed_segments'])) {
            return in_array($customerSegment, $restrictions['allowed_segments']);
        }

        if (isset($restrictions['blocked_segments'])) {
            return !in_array($customerSegment, $restrictions['blocked_segments']);
        }

        return true;
    }

    /**
     * Check custom conditions.
     */
    protected function matchesCustomConditions($context)
    {
        if (!$this->conditions) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if (!$this->evaluateCondition($condition, $context)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate a single condition.
     */
    protected function evaluateCondition($condition, $context)
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? 'equals';
        $value = $condition['value'] ?? null;

        if (!$field || !isset($context[$field])) {
            return false;
        }

        $contextValue = $context[$field];

        switch ($operator) {
            case 'equals':
                return $contextValue == $value;
            case 'not_equals':
                return $contextValue != $value;
            case 'greater_than':
                return $contextValue > $value;
            case 'less_than':
                return $contextValue < $value;
            case 'greater_than_or_equal':
                return $contextValue >= $value;
            case 'less_than_or_equal':
                return $contextValue <= $value;
            case 'in':
                return in_array($contextValue, (array) $value);
            case 'not_in':
                return !in_array($contextValue, (array) $value);
            case 'contains':
                return strpos($contextValue, $value) !== false;
            case 'starts_with':
                return strpos($contextValue, $value) === 0;
            case 'ends_with':
                return substr($contextValue, -strlen($value)) === $value;
            default:
                return false;
        }
    }

    /**
     * Record a successful transaction using this rule.
     */
    public function recordSuccess()
    {
        $this->increment('success_count');
        $this->updateSuccessRate();
    }

    /**
     * Record a failed transaction using this rule.
     */
    public function recordFailure()
    {
        $this->increment('failure_count');
        $this->updateSuccessRate();
    }

    /**
     * Update the success rate.
     */
    protected function updateSuccessRate()
    {
        $totalTransactions = $this->success_count + $this->failure_count;
        
        if ($totalTransactions > 0) {
            $this->success_rate = round(($this->success_count / $totalTransactions) * 100, 2);
            $this->save();
        }
    }

    /**
     * Get performance metrics.
     */
    public function getPerformanceMetrics()
    {
        $totalTransactions = $this->success_count + $this->failure_count;
        
        return [
            'total_transactions' => $totalTransactions,
            'successful_transactions' => $this->success_count,
            'failed_transactions' => $this->failure_count,
            'success_rate' => $this->success_rate,
            'usage_frequency' => $totalTransactions > 0 ? 'active' : 'unused',
        ];
    }

    /**
     * Clone rule for A/B testing.
     */
    public function cloneForTesting($trafficPercentage = 50)
    {
        $clone = $this->replicate();
        $clone->name = $this->name . ' (Test)';
        $clone->rule_type = 'ab_test';
        $clone->traffic_percentage = $trafficPercentage;
        $clone->priority = $this->priority - 1; // Lower priority
        $clone->success_count = 0;
        $clone->failure_count = 0;
        $clone->success_rate = 0;
        $clone->save();

        // Update original rule traffic percentage
        $this->traffic_percentage = 100 - $trafficPercentage;
        $this->save();

        return $clone;
    }

    /**
     * Deactivate rule.
     */
    public function deactivate($reason = null)
    {
        $this->is_active = false;
        
        if ($reason) {
            $metadata = $this->metadata ?? [];
            $metadata['deactivation_reason'] = $reason;
            $metadata['deactivated_at'] = now()->toISOString();
            $this->metadata = $metadata;
        }

        $this->save();
    }

    /**
     * Activate rule.
     */
    public function activate()
    {
        $this->is_active = true;
        
        $metadata = $this->metadata ?? [];
        $metadata['activated_at'] = now()->toISOString();
        $this->metadata = $metadata;

        $this->save();
    }
}
