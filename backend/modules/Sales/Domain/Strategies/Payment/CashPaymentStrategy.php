<?php

namespace Modules\Sales\Domain\Strategies\Payment;

class CashPaymentStrategy implements PaymentStrategyInterface
{
    public function process(array $orderData): array
    {
        return array_merge($orderData, [
            'pay_method'         => 'cash',
            'transaction_number' => null,
        ]);
    }

    public function validate(array $orderData): void
    {
        if (empty($orderData['amount_paid'])) {
            throw new \DomainException('Amount paid is required for cash payment.');
        }
    }

    public function getMethod(): string
    {
        return 'cash';
    }
}
