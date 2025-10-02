<?php

namespace Modules\Payment\Services;

use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Entities\PaymentRoutingRule;
use Modules\Payment\Entities\PaymentTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentRoutingService
{
    /**
     * Select the best payment method for a transaction.
     *
     * @param object $request
     * @param array $context
     * @return PaymentMethod|null
     */
    public function selectPaymentMethod($request, array $context): ?PaymentMethod
    {
        $availableMethods = $this->getAvailablePaymentMethods($context);
        
        if (empty($availableMethods)) {
            return null;
        }

        // Get active routing rules ordered by priority
        $routingRules = PaymentRoutingRule::active()
                                         ->effective()
                                         ->orderedByPriority()
                                         ->get();

        // Apply routing rules
        foreach ($routingRules as $rule) {
            if ($rule->matches($this->buildTransactionContext($request, $context))) {
                $targetMethod = $availableMethods->firstWhere('id', $rule->target_payment_method_id);
                
                if ($targetMethod && $this->isMethodAvailable($targetMethod, $context)) {
                    return $targetMethod;
                }

                // Try fallback method if target is not available
                if ($rule->fallback_payment_method_id) {
                    $fallbackMethod = $availableMethods->firstWhere('id', $rule->fallback_payment_method_id);
                    
                    if ($fallbackMethod && $this->isMethodAvailable($fallbackMethod, $context)) {
                        return $fallbackMethod;
                    }
                }
            }
        }

        // No routing rule matched, use default selection logic
        return $this->selectDefaultPaymentMethod($availableMethods, $context);
    }

    /**
     * Get available payment methods for context.
     *
     * @param array $context
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailablePaymentMethods(array $context)
    {
        $country = $context['country'] ?? null;
        $currency = $context['currency'] ?? 'USD';
        $amount = $context['amount'] ?? 0;

        return PaymentMethod::active()
                           ->where(function ($query) use ($country) {
                               if ($country) {
                                   $query->where('is_global', true)
                                         ->orWhereJsonContains('country_codes', $country);
                               } else {
                                   $query->where('is_global', true);
                               }
                           })
                           ->whereJsonContains('supported_currencies', $currency)
                           ->whereHas('limits', function ($query) use ($amount, $currency) {
                               if ($amount > 0) {
                                   $query->where('currency_id', function ($subQuery) use ($currency) {
                                       $subQuery->select('id')
                                               ->from('currencies')
                                               ->where('code', $currency);
                                   })
                                   ->where('limit_type', 'transaction')
                                   ->where(function ($limitQuery) use ($amount) {
                                       $limitQuery->whereNull('min_limit')
                                                 ->orWhere('min_limit', '<=', $amount);
                                   })
                                   ->where(function ($limitQuery) use ($amount) {
                                       $limitQuery->whereNull('max_limit')
                                                 ->orWhere('max_limit', '>=', $amount);
                                   });
                               }
                           }, '>=', 0) // Use >= 0 to include methods without limits
                           ->orderBy('priority', 'desc')
                           ->get();
    }

    /**
     * Record transaction result for routing rule optimization.
     *
     * @param PaymentMethod $paymentMethod
     * @param bool $success
     * @return void
     */
    public function recordTransactionResult(PaymentMethod $paymentMethod, bool $success): void
    {
        // Update payment method success rate
        if ($success) {
            $paymentMethod->increment('success_count', 1);
        } else {
            $paymentMethod->increment('failure_count', 1);
        }

        $paymentMethod->success_rate = $paymentMethod->calculateSuccessRate();
        $paymentMethod->save();

        // Update routing rules that led to this payment method
        $activeRules = PaymentRoutingRule::where('target_payment_method_id', $paymentMethod->id)
                                        ->orWhere('fallback_payment_method_id', $paymentMethod->id)
                                        ->active()
                                        ->get();

        foreach ($activeRules as $rule) {
            if ($success) {
                $rule->recordSuccess();
            } else {
                $rule->recordFailure();
            }
        }
    }

    /**
     * Create or update routing rule.
     *
     * @param array $data
     * @return PaymentRoutingRule
     */
    public function createRoutingRule(array $data): PaymentRoutingRule
    {
        return PaymentRoutingRule::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'conditions' => $data['conditions'] ?? [],
            'priority' => $data['priority'] ?? 0,
            'target_payment_method_id' => $data['target_payment_method_id'],
            'fallback_payment_method_id' => $data['fallback_payment_method_id'] ?? null,
            'rule_type' => $data['rule_type'] ?? 'primary',
            'traffic_percentage' => $data['traffic_percentage'] ?? 100,
            'time_restrictions' => $data['time_restrictions'] ?? null,
            'amount_restrictions' => $data['amount_restrictions'] ?? null,
            'geographic_restrictions' => $data['geographic_restrictions'] ?? null,
            'customer_segment_restrictions' => $data['customer_segment_restrictions'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'effective_from' => $data['effective_from'] ?? null,
            'effective_until' => $data['effective_until'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    /**
     * Get routing analytics.
     *
     * @param array $filters
     * @return array
     */
    public function getRoutingAnalytics(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->subDays(30);
        $endDate = $filters['end_date'] ?? now();

        // Get transaction distribution by payment method
        $methodDistribution = PaymentTransaction::whereBetween('created_at', [$startDate, $endDate])
                                              ->selectRaw('payment_method_id, COUNT(*) as transaction_count, 
                                                          SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as successful_count,
                                                          SUM(amount) as total_amount')
                                              ->groupBy('payment_method_id')
                                              ->with('paymentMethod')
                                              ->get();

        // Get routing rule performance
        $rulePerformance = PaymentRoutingRule::active()
                                           ->get()
                                           ->map(function ($rule) {
                                               return [
                                                   'id' => $rule->id,
                                                   'name' => $rule->name,
                                                   'success_rate' => $rule->success_rate,
                                                   'total_transactions' => $rule->success_count + $rule->failure_count,
                                                   'performance_metrics' => $rule->getPerformanceMetrics(),
                                               ];
                                           });

        return [
            'method_distribution' => $methodDistribution,
            'rule_performance' => $rulePerformance,
            'total_transactions' => $methodDistribution->sum('transaction_count'),
            'overall_success_rate' => $methodDistribution->sum('transaction_count') > 0 
                ? round(($methodDistribution->sum('successful_count') / $methodDistribution->sum('transaction_count')) * 100, 2)
                : 0,
        ];
    }

    /**
     * Optimize routing rules based on performance data.
     *
     * @return array
     */
    public function optimizeRoutingRules(): array
    {
        $optimizations = [];
        $rules = PaymentRoutingRule::active()->get();

        foreach ($rules as $rule) {
            $metrics = $rule->getPerformanceMetrics();
            
            // Suggest optimizations based on performance
            if ($metrics['success_rate'] < 80 && $metrics['total_transactions'] > 100) {
                $optimizations[] = [
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'type' => 'low_success_rate',
                    'current_rate' => $metrics['success_rate'],
                    'suggestion' => 'Consider reviewing conditions or switching to a more reliable payment method',
                ];
            }

            if ($metrics['usage_frequency'] === 'unused') {
                $optimizations[] = [
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'type' => 'unused_rule',
                    'suggestion' => 'Rule has not been used. Consider reviewing conditions or deactivating',
                ];
            }
        }

        return $optimizations;
    }

    /**
     * Build transaction context for rule matching.
     *
     * @param object $request
     * @param array $context
     * @return array
     */
    protected function buildTransactionContext($request, array $context): array
    {
        return [
            'amount' => method_exists($request, 'getAmount') ? $request->getAmount() : ($context['amount'] ?? 0),
            'currency' => method_exists($request, 'getCurrency') ? $request->getCurrency() : ($context['currency'] ?? 'USD'),
            'customer_id' => method_exists($request, 'getCustomerId') ? $request->getCustomerId() : ($context['customer_id'] ?? null),
            'country' => $context['country'] ?? null,
            'customer_segment' => $context['customer_segment'] ?? 'all',
            'is_recurring' => $context['is_recurring'] ?? false,
            'risk_score' => $context['risk_score'] ?? 0,
            'time_of_day' => now()->hour,
            'day_of_week' => now()->dayOfWeek,
        ];
    }

    /**
     * Check if payment method is available.
     *
     * @param PaymentMethod $paymentMethod
     * @param array $context
     * @return bool
     */
    protected function isMethodAvailable(PaymentMethod $paymentMethod, array $context): bool
    {
        // Check if method is active
        if ($paymentMethod->status !== 'active') {
            return false;
        }

        // Check currency support
        $currency = $context['currency'] ?? 'USD';
        if (!in_array($currency, $paymentMethod->supported_currencies ?? [])) {
            return false;
        }

        // Check country support
        $country = $context['country'] ?? null;
        if ($country && !$paymentMethod->is_global && !in_array($country, $paymentMethod->country_codes ?? [])) {
            return false;
        }

        // Check amount limits
        $amount = $context['amount'] ?? 0;
        if ($amount > 0) {
            $limits = $paymentMethod->limits()
                                   ->where('limit_type', 'transaction')
                                   ->whereHas('currency', function ($query) use ($currency) {
                                       $query->where('code', $currency);
                                   })
                                   ->first();

            if ($limits && !$limits->isAmountAllowed($amount, $context['customer_segment'] ?? 'all', $country)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Select default payment method when no routing rules match.
     *
     * @param \Illuminate\Database\Eloquent\Collection $availableMethods
     * @param array $context
     * @return PaymentMethod|null
     */
    protected function selectDefaultPaymentMethod($availableMethods, array $context): ?PaymentMethod
    {
        if ($availableMethods->isEmpty()) {
            return null;
        }

        // Sort by success rate and priority
        return $availableMethods->sortByDesc(function ($method) {
            return ($method->success_rate * 0.7) + ($method->priority * 0.3);
        })->first();
    }
}
