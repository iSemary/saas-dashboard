<?php

namespace Modules\Sales\Domain\Strategies\Payment;

interface PaymentStrategyInterface
{
    public function process(array $orderData): array;
    public function validate(array $orderData): void;
    public function getMethod(): string;
}
