<?php

namespace Modules\Payment\Services;

use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Entities\PaymentMethodFee;
use Modules\Utilities\Entities\Currency;
use Illuminate\Support\Facades\Cache;

class FeeCalculationService
{
    /**
     * Calculate fees for a payment.
     *
     * @param PaymentMethod $paymentMethod
     * @param float $amount
     * @param string $currencyCode
     * @param array $context
     * @return array
     */
    public function calculateFees(PaymentMethod $paymentMethod, float $amount, string $currencyCode, array $context = []): array
    {
        $currency = Currency::where('code', $currencyCode)->first();
        
        if (!$currency) {
            throw new \InvalidArgumentException("Currency not found: {$currencyCode}");
        }

        $customerSegment = $context['customer_segment'] ?? 'all';
        $region = $context['region'] ?? $context['country'] ?? null;
        $transactionType = $context['transaction_type'] ?? 'sale';

        // Get applicable fees
        $fees = $this->getApplicableFees($paymentMethod, $currency, $customerSegment, $region, $transactionType);
        
        $breakdown = [
            'base_amount' => $amount,
            'currency' => $currencyCode,
            'fees' => [],
            'total' => 0,
        ];

        foreach ($fees as $fee) {
            $feeAmount = $fee->calculateFee($amount, $customerSegment, $region);
            
            if ($feeAmount > 0) {
                $feeData = [
                    'id' => $fee->id,
                    'type' => $fee->fee_type,
                    'description' => $this->getFeeDescription($fee),
                    'amount' => round($feeAmount, 2),
                    'applies_to' => $fee->applies_to,
                ];

                $breakdown['fees'][] = $feeData;
                $breakdown['total'] += $feeAmount;
            }
        }

        // Add any additional fees (currency conversion, etc.)
        $additionalFees = $this->calculateAdditionalFees($paymentMethod, $amount, $currencyCode, $context);
        
        foreach ($additionalFees as $additionalFee) {
            $breakdown['fees'][] = $additionalFee;
            $breakdown['total'] += $additionalFee['amount'];
        }

        $breakdown['total'] = round($breakdown['total'], 2);
        $breakdown['net_amount'] = round($amount - $breakdown['total'], 2);

        return $breakdown;
    }

    /**
     * Calculate refund fees.
     *
     * @param PaymentMethod $paymentMethod
     * @param float $refundAmount
     * @param float $originalAmount
     * @param string $currencyCode
     * @param array $context
     * @return array
     */
    public function calculateRefundFees(
        PaymentMethod $paymentMethod,
        float $refundAmount,
        float $originalAmount,
        string $currencyCode,
        array $context = []
    ): array {
        $currency = Currency::where('code', $currencyCode)->first();
        
        if (!$currency) {
            throw new \InvalidArgumentException("Currency not found: {$currencyCode}");
        }

        $customerSegment = $context['customer_segment'] ?? 'all';
        $region = $context['region'] ?? $context['country'] ?? null;

        // Get refund-specific fees
        $fees = $this->getApplicableFees($paymentMethod, $currency, $customerSegment, $region, 'refund');
        
        $breakdown = [
            'refund_amount' => $refundAmount,
            'original_amount' => $originalAmount,
            'currency' => $currencyCode,
            'fees' => [],
            'total' => 0,
        ];

        foreach ($fees as $fee) {
            $feeAmount = $fee->calculateFee($refundAmount, $customerSegment, $region);
            
            if ($feeAmount > 0) {
                $feeData = [
                    'id' => $fee->id,
                    'type' => $fee->fee_type,
                    'description' => $this->getFeeDescription($fee),
                    'amount' => round($feeAmount, 2),
                    'applies_to' => $fee->applies_to,
                ];

                $breakdown['fees'][] = $feeData;
                $breakdown['total'] += $feeAmount;
            }
        }

        // Calculate fee refunds (some gateways refund processing fees)
        $feeRefunds = $this->calculateFeeRefunds($paymentMethod, $refundAmount, $originalAmount, $context);
        $breakdown['fee_refunds'] = $feeRefunds;

        $breakdown['total'] = round($breakdown['total'], 2);
        $breakdown['net_refund'] = round($refundAmount - $breakdown['total'] + $feeRefunds['total'], 2);

        return $breakdown;
    }

    /**
     * Get fee estimate for display purposes.
     *
     * @param PaymentMethod $paymentMethod
     * @param float $amount
     * @param string $currencyCode
     * @param array $context
     * @return array
     */
    public function getFeeEstimate(PaymentMethod $paymentMethod, float $amount, string $currencyCode, array $context = []): array
    {
        $cacheKey = "fee_estimate_{$paymentMethod->id}_{$amount}_{$currencyCode}_" . md5(serialize($context));
        
        return Cache::remember($cacheKey, 300, function () use ($paymentMethod, $amount, $currencyCode, $context) {
            return $this->calculateFees($paymentMethod, $amount, $currencyCode, $context);
        });
    }

    /**
     * Compare fees across payment methods.
     *
     * @param array $paymentMethods
     * @param float $amount
     * @param string $currencyCode
     * @param array $context
     * @return array
     */
    public function compareFees(array $paymentMethods, float $amount, string $currencyCode, array $context = []): array
    {
        $comparison = [];
        
        foreach ($paymentMethods as $paymentMethod) {
            $fees = $this->calculateFees($paymentMethod, $amount, $currencyCode, $context);
            
            $comparison[] = [
                'payment_method_id' => $paymentMethod->id,
                'payment_method_name' => $paymentMethod->name,
                'total_fees' => $fees['total'],
                'net_amount' => $fees['net_amount'],
                'fee_percentage' => $amount > 0 ? round(($fees['total'] / $amount) * 100, 2) : 0,
                'fees_breakdown' => $fees['fees'],
            ];
        }

        // Sort by total fees (lowest first)
        usort($comparison, function ($a, $b) {
            return $a['total_fees'] <=> $b['total_fees'];
        });

        return $comparison;
    }

    /**
     * Get applicable fees for a payment method.
     *
     * @param PaymentMethod $paymentMethod
     * @param Currency $currency
     * @param string $customerSegment
     * @param string|null $region
     * @param string $transactionType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getApplicableFees(
        PaymentMethod $paymentMethod,
        Currency $currency,
        string $customerSegment,
        ?string $region,
        string $transactionType
    ) {
        return $paymentMethod->fees()
                            ->where('currency_id', $currency->id)
                            ->active()
                            ->effective()
                            ->appliesTo($transactionType)
                            ->forCustomerSegment($customerSegment)
                            ->forRegion($region)
                            ->orderBy('priority', 'desc')
                            ->get();
    }

    /**
     * Calculate additional fees (currency conversion, etc.).
     *
     * @param PaymentMethod $paymentMethod
     * @param float $amount
     * @param string $currencyCode
     * @param array $context
     * @return array
     */
    protected function calculateAdditionalFees(PaymentMethod $paymentMethod, float $amount, string $currencyCode, array $context): array
    {
        $additionalFees = [];

        // Currency conversion fee
        if ($this->requiresCurrencyConversion($paymentMethod, $currencyCode)) {
            $conversionFeeRate = $this->getCurrencyConversionFeeRate($paymentMethod, $currencyCode);
            $conversionFee = ($amount * $conversionFeeRate) / 100;
            
            if ($conversionFee > 0) {
                $additionalFees[] = [
                    'type' => 'currency_conversion',
                    'description' => 'Currency conversion fee',
                    'amount' => round($conversionFee, 2),
                    'rate' => $conversionFeeRate,
                ];
            }
        }

        // Cross-border fee
        if ($this->isCrossBorderTransaction($paymentMethod, $context)) {
            $crossBorderFeeRate = $this->getCrossBorderFeeRate($paymentMethod);
            $crossBorderFee = ($amount * $crossBorderFeeRate) / 100;
            
            if ($crossBorderFee > 0) {
                $additionalFees[] = [
                    'type' => 'cross_border',
                    'description' => 'Cross-border transaction fee',
                    'amount' => round($crossBorderFee, 2),
                    'rate' => $crossBorderFeeRate,
                ];
            }
        }

        // Risk assessment fee
        if ($this->isHighRiskTransaction($amount, $context)) {
            $riskFee = $this->calculateRiskFee($amount, $context);
            
            if ($riskFee > 0) {
                $additionalFees[] = [
                    'type' => 'risk_assessment',
                    'description' => 'High-risk transaction fee',
                    'amount' => round($riskFee, 2),
                ];
            }
        }

        return $additionalFees;
    }

    /**
     * Calculate fee refunds.
     *
     * @param PaymentMethod $paymentMethod
     * @param float $refundAmount
     * @param float $originalAmount
     * @param array $context
     * @return array
     */
    protected function calculateFeeRefunds(PaymentMethod $paymentMethod, float $refundAmount, float $originalAmount, array $context): array
    {
        $feeRefunds = [
            'items' => [],
            'total' => 0,
        ];

        // Check gateway-specific fee refund policies
        $refundPolicy = $this->getFeeRefundPolicy($paymentMethod);
        
        if ($refundPolicy['refunds_processing_fees']) {
            $processingFeeRefund = $this->calculateProcessingFeeRefund($refundAmount, $originalAmount, $context);
            
            if ($processingFeeRefund > 0) {
                $feeRefunds['items'][] = [
                    'type' => 'processing_fee_refund',
                    'description' => 'Processing fee refund',
                    'amount' => round($processingFeeRefund, 2),
                ];
                $feeRefunds['total'] += $processingFeeRefund;
            }
        }

        return $feeRefunds;
    }

    /**
     * Get fee description.
     *
     * @param PaymentMethodFee $fee
     * @return string
     */
    protected function getFeeDescription(PaymentMethodFee $fee): string
    {
        $descriptions = [
            'percentage' => 'Processing fee',
            'fixed' => 'Transaction fee',
            'tiered' => 'Tiered processing fee',
            'mixed' => 'Processing fee',
        ];

        $baseDescription = $descriptions[$fee->fee_type] ?? 'Fee';
        
        if ($fee->customer_segment !== 'all') {
            $baseDescription .= " ({$fee->customer_segment})";
        }

        return $baseDescription;
    }

    /**
     * Check if currency conversion is required.
     *
     * @param PaymentMethod $paymentMethod
     * @param string $currencyCode
     * @return bool
     */
    protected function requiresCurrencyConversion(PaymentMethod $paymentMethod, string $currencyCode): bool
    {
        $supportedCurrencies = $paymentMethod->supported_currencies ?? [];
        return !in_array($currencyCode, $supportedCurrencies);
    }

    /**
     * Get currency conversion fee rate.
     *
     * @param PaymentMethod $paymentMethod
     * @param string $currencyCode
     * @return float
     */
    protected function getCurrencyConversionFeeRate(PaymentMethod $paymentMethod, string $currencyCode): float
    {
        // Default conversion fee rate (can be configured per gateway)
        return $paymentMethod->metadata['currency_conversion_fee_rate'] ?? 2.5; // 2.5%
    }

    /**
     * Check if this is a cross-border transaction.
     *
     * @param PaymentMethod $paymentMethod
     * @param array $context
     * @return bool
     */
    protected function isCrossBorderTransaction(PaymentMethod $paymentMethod, array $context): bool
    {
        $merchantCountry = config('payment.merchant_country', 'US');
        $customerCountry = $context['country'] ?? $merchantCountry;
        
        return $merchantCountry !== $customerCountry;
    }

    /**
     * Get cross-border fee rate.
     *
     * @param PaymentMethod $paymentMethod
     * @return float
     */
    protected function getCrossBorderFeeRate(PaymentMethod $paymentMethod): float
    {
        return $paymentMethod->metadata['cross_border_fee_rate'] ?? 1.5; // 1.5%
    }

    /**
     * Check if this is a high-risk transaction.
     *
     * @param float $amount
     * @param array $context
     * @return bool
     */
    protected function isHighRiskTransaction(float $amount, array $context): bool
    {
        $highRiskThreshold = config('payment.high_risk_threshold', 10000);
        
        if ($amount > $highRiskThreshold) {
            return true;
        }

        // Check for other risk factors
        $riskFactors = [
            'new_customer' => $context['is_new_customer'] ?? false,
            'high_risk_country' => in_array($context['country'] ?? '', config('payment.high_risk_countries', [])),
            'unusual_pattern' => $context['unusual_transaction_pattern'] ?? false,
        ];

        return array_sum($riskFactors) >= 2; // 2 or more risk factors
    }

    /**
     * Calculate risk fee.
     *
     * @param float $amount
     * @param array $context
     * @return float
     */
    protected function calculateRiskFee(float $amount, array $context): float
    {
        $riskFeeRate = config('payment.risk_fee_rate', 0.5); // 0.5%
        return ($amount * $riskFeeRate) / 100;
    }

    /**
     * Get fee refund policy for payment method.
     *
     * @param PaymentMethod $paymentMethod
     * @return array
     */
    protected function getFeeRefundPolicy(PaymentMethod $paymentMethod): array
    {
        $defaultPolicy = [
            'refunds_processing_fees' => false,
            'partial_refund_fee_proration' => true,
        ];

        return array_merge($defaultPolicy, $paymentMethod->metadata['fee_refund_policy'] ?? []);
    }

    /**
     * Calculate processing fee refund.
     *
     * @param float $refundAmount
     * @param float $originalAmount
     * @param array $context
     * @return float
     */
    protected function calculateProcessingFeeRefund(float $refundAmount, float $originalAmount, array $context): float
    {
        $originalFees = $context['original_fees'] ?? 0;
        
        if ($originalFees <= 0) {
            return 0;
        }

        // For full refunds, refund all processing fees
        if ($refundAmount >= $originalAmount) {
            return $originalFees;
        }

        // For partial refunds, prorate the fee refund
        $refundRatio = $refundAmount / $originalAmount;
        return $originalFees * $refundRatio;
    }
}
