<?php

namespace Modules\Sales\Domain\Strategies\OrderType;

class TakeawayOrderStrategy implements OrderTypeStrategyInterface
{
    public function prepare(array $orderData): array
    {
        return array_merge($orderData, ['order_type' => 'takeaway']);
    }

    public function getType(): string
    {
        return 'takeaway';
    }
}
