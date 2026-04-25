<?php

namespace Modules\Sales\Domain\Strategies\Payment;

class InstallmentPaymentStrategy implements PaymentStrategyInterface
{
    public function process(array $orderData): array
    {
        return array_merge($orderData, [
            'pay_method' => 'installment',
        ]);
    }

    public function validate(array $orderData): void
    {
        if (empty($orderData['total_months']) || $orderData['total_months'] < 1) {
            throw new \DomainException('Total months is required for installment payment.');
        }
        if (empty($orderData['monthly_amount']) || $orderData['monthly_amount'] <= 0) {
            throw new \DomainException('Monthly amount is required for installment payment.');
        }
    }

    public function getMethod(): string
    {
        return 'installment';
    }
}
