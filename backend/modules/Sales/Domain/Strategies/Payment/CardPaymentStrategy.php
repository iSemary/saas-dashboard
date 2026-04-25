<?php

namespace Modules\Sales\Domain\Strategies\Payment;

class CardPaymentStrategy implements PaymentStrategyInterface
{
    public function process(array $orderData): array
    {
        return array_merge($orderData, [
            'pay_method'  => 'card',
            'amount_paid' => $orderData['total_price'] ?? $orderData['amount_paid'],
        ]);
    }

    public function validate(array $orderData): void
    {
        if (empty($orderData['transaction_number'])) {
            throw new \DomainException('Transaction number is required for card payment.');
        }
    }

    public function getMethod(): string
    {
        return 'card';
    }
}
