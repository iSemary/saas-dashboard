<?php

namespace Modules\Sales\Domain\Strategies\OrderType;

class DeliveryOrderStrategy implements OrderTypeStrategyInterface
{
    public function prepare(array $orderData): array
    {
        return array_merge($orderData, ['order_type' => 'delivery']);
    }

    public function getType(): string
    {
        return 'delivery';
    }
}
