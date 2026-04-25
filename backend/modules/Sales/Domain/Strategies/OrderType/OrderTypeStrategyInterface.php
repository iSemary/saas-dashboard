<?php

namespace Modules\Sales\Domain\Strategies\OrderType;

interface OrderTypeStrategyInterface
{
    public function prepare(array $orderData): array;
    public function getType(): string;
}
