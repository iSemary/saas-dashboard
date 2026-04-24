<?php

namespace Modules\Payment\Services;

use Modules\Payment\DTOs\PaymentRequest;
use Modules\Payment\Entities\PaymentTransaction;
use Modules\Payment\Exceptions\PaymentGatewayException;
use Modules\Utilities\Entities\Currency;
use Illuminate\Support\Facades\Validator;

class PaymentValidationService
{
    /**
     * Validate payment request.
     *
     * @param PaymentRequest $request
     * @param array $context
     * @throws PaymentGatewayException
     */
    public function validatePaymentRequest(PaymentRequest $request, array $context = []): void
    {
        // Basic validation
        if ($request->getAmount() <= 0) {
            throw new PaymentGatewayException("Payment amount must be greater than zero");
        }

        if (!$request->getCurrency()) {
            throw new PaymentGatewayException("Currency is required");
        }

        // Validate currency exists and is active
        $currency = Currency::where('code', $request->getCurrency())->active()->first();
        if (!$currency) {
            throw new PaymentGatewayException("Invalid or inactive currency: " . $request->getCurrency());
        }

        // Validate amount precision
        $maxDecimalPlaces = $currency->decimal_places;
        $decimalPlaces = strlen(substr(strrchr($request->getAmount(), "."), 1));
        if ($decimalPlaces > $maxDecimalPlaces) {
            throw new PaymentGatewayException("Amount has too many decimal places for currency {$request->getCurrency()}");
        }

        // Validate payment method data if provided
        if ($request->getPaymentMethodData()) {
            $this->validatePaymentMethodData($request->getPaymentMethodData());
        }

        // Validate addresses if provided
        if ($request->getBillingAddress()) {
            $this->validateAddress($request->getBillingAddress(), 'billing');
        }

        if ($request->getShippingAddress()) {
            $this->validateAddress($request->getShippingAddress(), 'shipping');
        }

        // Additional business rule validations
        $this->validateBusinessRules($request, $context);
    }

    /**
     * Validate refund request.
     *
     * @param object $request
     * @param PaymentTransaction $originalTransaction
     * @param array $context
     * @throws PaymentGatewayException
     */
    public function validateRefundRequest($request, PaymentTransaction $originalTransaction, array $context = []): void
    {
        // Check if original transaction can be refunded
        if (!$originalTransaction->canBeRefunded()) {
            throw new PaymentGatewayException("Transaction cannot be refunded");
        }

        // Validate refund amount
        $refundAmount = method_exists($request, 'getAmount') ? $request->getAmount() : 0;
        if ($refundAmount <= 0) {
            throw new PaymentGatewayException("Refund amount must be greater than zero");
        }

        $refundableAmount = $originalTransaction->getRefundableAmount();
        if ($refundAmount > $refundableAmount) {
            throw new PaymentGatewayException("Refund amount ({$refundAmount}) exceeds refundable amount ({$refundableAmount})");
        }

        // Check refund time limits
        $this->validateRefundTimeLimit($originalTransaction);

        // Validate refund reason if provided
        $reason = method_exists($request, 'getReason') ? $request->getReason() : null;
        if ($reason && !$this->isValidRefundReason($reason)) {
            throw new PaymentGatewayException("Invalid refund reason: {$reason}");
        }
    }

    /**
     * Validate payment method data.
     *
     * @param array $data
     * @throws PaymentGatewayException
     */
    protected function validatePaymentMethodData(array $data): void
    {
        $type = $data['type'] ?? null;
        
        if (!$type) {
            throw new PaymentGatewayException("Payment method type is required");
        }

        switch ($type) {
            case 'card':
                $this->validateCardData($data['card'] ?? []);
                break;
            case 'bank_account':
                $this->validateBankAccountData($data['bank_account'] ?? []);
                break;
            case 'wallet':
                $this->validateWalletData($data['wallet'] ?? []);
                break;
        }
    }

    /**
     * Validate card data.
     *
     * @param array $cardData
     * @throws PaymentGatewayException
     */
    protected function validateCardData(array $cardData): void
    {
        $validator = Validator::make($cardData, [
            'number' => 'required|string|min:13|max:19',
            'exp_month' => 'required|integer|between:1,12',
            'exp_year' => 'required|integer|min:' . date('Y'),
            'cvc' => 'required|string|min:3|max:4',
            'name' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new PaymentGatewayException("Invalid card data: " . implode(', ', $validator->errors()->all()));
        }

        // Additional card validation
        if (!$this->isValidCardNumber($cardData['number'])) {
            throw new PaymentGatewayException("Invalid card number");
        }

        if (!$this->isValidExpiryDate($cardData['exp_month'], $cardData['exp_year'])) {
            throw new PaymentGatewayException("Card has expired");
        }
    }

    /**
     * Validate bank account data.
     *
     * @param array $bankData
     * @throws PaymentGatewayException
     */
    protected function validateBankAccountData(array $bankData): void
    {
        $validator = Validator::make($bankData, [
            'account_number' => 'required|string',
            'routing_number' => 'required|string',
            'account_holder_name' => 'required|string|max:255',
            'account_type' => 'sometimes|in:checking,savings',
        ]);

        if ($validator->fails()) {
            throw new PaymentGatewayException("Invalid bank account data: " . implode(', ', $validator->errors()->all()));
        }
    }

    /**
     * Validate wallet data.
     *
     * @param array $walletData
     * @throws PaymentGatewayException
     */
    protected function validateWalletData(array $walletData): void
    {
        $validator = Validator::make($walletData, [
            'provider' => 'required|string|in:paypal,apple_pay,google_pay,amazon_pay',
            'token' => 'sometimes|string',
            'email' => 'sometimes|email',
        ]);

        if ($validator->fails()) {
            throw new PaymentGatewayException("Invalid wallet data: " . implode(', ', $validator->errors()->all()));
        }
    }

    /**
     * Validate address.
     *
     * @param array $address
     * @param string $type
     * @throws PaymentGatewayException
     */
    protected function validateAddress(array $address, string $type): void
    {
        $validator = Validator::make($address, [
            'line1' => 'required|string|max:255',
            'line2' => 'sometimes|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'sometimes|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|size:2', // ISO 3166-1 alpha-2
        ]);

        if ($validator->fails()) {
            throw new PaymentGatewayException("Invalid {$type} address: " . implode(', ', $validator->errors()->all()));
        }
    }

    /**
     * Validate business rules.
     *
     * @param PaymentRequest $request
     * @param array $context
     * @throws PaymentGatewayException
     */
    protected function validateBusinessRules(PaymentRequest $request, array $context): void
    {
        // Check minimum/maximum transaction amounts
        $this->validateTransactionLimits($request->getAmount(), $request->getCurrency(), $context);

        // Check daily/monthly limits for customer
        if ($request->getCustomerId()) {
            $this->validateCustomerLimits($request->getCustomerId(), $request->getAmount(), $request->getCurrency());
        }

        // Check for suspicious patterns
        $this->validateFraudRules($request, $context);

        // Check business hours if configured
        $this->validateBusinessHours($context);
    }

    /**
     * Validate transaction limits.
     *
     * @param float $amount
     * @param string $currency
     * @param array $context
     * @throws PaymentGatewayException
     */
    protected function validateTransactionLimits(float $amount, string $currency, array $context): void
    {
        $minAmount = config('payment.limits.min_transaction_amount', 0.50);
        $maxAmount = config('payment.limits.max_transaction_amount', 100000);

        if ($amount < $minAmount) {
            throw new PaymentGatewayException("Transaction amount is below minimum limit ({$minAmount} {$currency})");
        }

        if ($amount > $maxAmount) {
            throw new PaymentGatewayException("Transaction amount exceeds maximum limit ({$maxAmount} {$currency})");
        }
    }

    /**
     * Validate customer limits.
     *
     * @param string $customerId
     * @param float $amount
     * @param string $currency
     * @throws PaymentGatewayException
     */
    protected function validateCustomerLimits(string $customerId, float $amount, string $currency): void
    {
        // Check daily limit
        $dailyLimit = config('payment.limits.daily_per_customer', 50000);
        $dailySpent = PaymentTransaction::where('customer_id', $customerId)
                                       ->where('created_at', '>=', now()->startOfDay())
                                       ->where('status', 'completed')
                                       ->sum('amount');

        if (($dailySpent + $amount) > $dailyLimit) {
            throw new PaymentGatewayException("Transaction would exceed daily spending limit");
        }

        // Check monthly limit
        $monthlyLimit = config('payment.limits.monthly_per_customer', 200000);
        $monthlySpent = PaymentTransaction::where('customer_id', $customerId)
                                         ->where('created_at', '>=', now()->startOfMonth())
                                         ->where('status', 'completed')
                                         ->sum('amount');

        if (($monthlySpent + $amount) > $monthlyLimit) {
            throw new PaymentGatewayException("Transaction would exceed monthly spending limit");
        }
    }

    /**
     * Validate fraud rules.
     *
     * @param PaymentRequest $request
     * @param array $context
     * @throws PaymentGatewayException
     */
    protected function validateFraudRules(PaymentRequest $request, array $context): void
    {
        $customerId = $request->getCustomerId();
        
        if (!$customerId) {
            return; // Skip fraud checks for guest transactions
        }

        // Check for rapid successive transactions
        $recentTransactions = PaymentTransaction::where('customer_id', $customerId)
                                              ->where('created_at', '>=', now()->subMinutes(5))
                                              ->count();

        if ($recentTransactions >= 5) {
            throw new PaymentGatewayException("Too many transactions in a short period. Please wait before trying again.");
        }

        // Check for unusual amount patterns
        $avgTransactionAmount = PaymentTransaction::where('customer_id', $customerId)
                                                 ->where('status', 'completed')
                                                 ->avg('amount');

        if ($avgTransactionAmount && $request->getAmount() > ($avgTransactionAmount * 10)) {
            // Flag for manual review rather than blocking
            $context['requires_manual_review'] = true;
        }
    }

    /**
     * Validate business hours.
     *
     * @param array $context
     * @throws PaymentGatewayException
     */
    protected function validateBusinessHours(array $context): void
    {
        $businessHoursEnabled = config('payment.business_hours.enabled', false);
        
        if (!$businessHoursEnabled) {
            return;
        }

        $now = now();
        $startHour = config('payment.business_hours.start', 9);
        $endHour = config('payment.business_hours.end', 17);
        $allowedDays = config('payment.business_hours.days', [1, 2, 3, 4, 5]); // Mon-Fri

        if (!in_array($now->dayOfWeek, $allowedDays) || 
            $now->hour < $startHour || 
            $now->hour >= $endHour) {
            throw new PaymentGatewayException("Transactions are only allowed during business hours");
        }
    }

    /**
     * Validate refund time limit.
     *
     * @param PaymentTransaction $transaction
     * @throws PaymentGatewayException
     */
    protected function validateRefundTimeLimit(PaymentTransaction $transaction): void
    {
        $refundTimeLimit = config('payment.refund_time_limit_days', 180); // 6 months default
        
        if ($transaction->created_at->addDays($refundTimeLimit)->isPast()) {
            throw new PaymentGatewayException("Refund time limit has expired");
        }
    }

    /**
     * Check if refund reason is valid.
     *
     * @param string $reason
     * @return bool
     */
    protected function isValidRefundReason(string $reason): bool
    {
        $validReasons = [
            'requested_by_customer',
            'duplicate',
            'fraudulent',
            'subscription_cancellation',
            'other'
        ];

        return in_array($reason, $validReasons);
    }

    /**
     * Validate card number using Luhn algorithm.
     *
     * @param string $cardNumber
     * @return bool
     */
    protected function isValidCardNumber(string $cardNumber): bool
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }

        // Luhn algorithm
        $sum = 0;
        $alternate = false;
        
        for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
            $digit = intval($cardNumber[$i]);
            
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = ($digit % 10) + 1;
                }
            }
            
            $sum += $digit;
            $alternate = !$alternate;
        }

        return ($sum % 10) === 0;
    }

    /**
     * Check if expiry date is valid.
     *
     * @param int $month
     * @param int $year
     * @return bool
     */
    protected function isValidExpiryDate(int $month, int $year): bool
    {
        $now = now();
        $expiryDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        return $expiryDate->isFuture();
    }
}
