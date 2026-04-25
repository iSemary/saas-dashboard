<?php

namespace Modules\Sales\Domain\Strategies\OrderType;

class DineInOrderStrategy implements OrderTypeStrategyInterface
{
    public function prepare(array $orderData): array
    {
        return array_merge($orderData, ['order_type' => 'dine_in']);
    }

    public function getType(): string
    {
        return 'dine_in';
    }
}
