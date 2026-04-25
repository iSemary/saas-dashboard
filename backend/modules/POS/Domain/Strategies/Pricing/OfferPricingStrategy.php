<?php

namespace Modules\POS\Domain\Strategies\Pricing;

use Modules\POS\Domain\ValueObjects\Money;

class OfferPricingStrategy implements PricingStrategyInterface
{
    public function calculate(float $basePrice, float $quantity, array $context = []): Money
    {
        $percent = (float) ($context['offer_percentage'] ?? 0);
        $discounted = $basePrice * (1 - $percent / 100);
        return new Money(round($discounted * $quantity, 2));
    }

    public function supports(string $type): bool
    {
        return $type === 'offer';
    }
}
