<?php

namespace Modules\POS\Domain\Strategies\Pricing;

use Modules\POS\Domain\ValueObjects\Money;

class WholesalePricingStrategy implements PricingStrategyInterface
{
    public function calculate(float $basePrice, float $quantity, array $context = []): Money
    {
        $wholesalePrice = (float) ($context['wholesale_price'] ?? $basePrice);
        return new Money(round($wholesalePrice * $quantity, 2));
    }

    public function supports(string $type): bool
    {
        return $type === 'wholesale';
    }
}
