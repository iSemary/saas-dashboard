<?php

namespace Modules\POS\Domain\Strategies\Pricing;

use Modules\POS\Domain\ValueObjects\Money;

class RegularPricingStrategy implements PricingStrategyInterface
{
    public function calculate(float $basePrice, float $quantity, array $context = []): Money
    {
        return new Money(round($basePrice * $quantity, 2));
    }

    public function supports(string $type): bool
    {
        return $type === 'regular';
    }
}
